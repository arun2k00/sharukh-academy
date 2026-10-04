<?php

namespace App\Support;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Every uploaded image is re-encoded to WebP and capped in size before it is
 * stored: phone photos and export-quality PNGs routinely arrive at several
 * megabytes, and on shared hosting that is both disk and page weight.
 *
 * Re-encoding also strips EXIF, so a student avatar cannot quietly carry the
 * GPS coordinates of where it was taken.
 */
class WebpImage
{
    /** Longest edge kept, in pixels. Generous enough for certificate artwork. */
    private const MAX_EDGE = 2400;

    private const QUALITY = 82;

    /**
     * Store an uploaded image as WebP and return its path on the disk.
     *
     * Falls back to storing the file untouched if GD cannot read it, so a
     * format this build has no support for is never silently lost.
     */
    public static function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $image instanceof GdImage) {
            Log::warning('Upload could not be re-encoded to WebP; stored as-is.', [
                'original' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
            ]);

            return $file->store($directory, $disk);
        }

        $image = self::downscale($image);

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        ob_start();
        imagewebp($image, null, self::QUALITY);
        $encoded = (string) ob_get_clean();
        imagedestroy($image);

        $path = rtrim($directory, '/').'/'.Str::random(40).'.webp';
        Storage::disk($disk)->put($path, $encoded);

        return $path;
    }

    private static function downscale(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $longest = max($width, $height);

        if ($longest <= self::MAX_EDGE) {
            return $image;
        }

        $scale = self::MAX_EDGE / $longest;
        $targetWidth = (int) round($width * $scale);
        $targetHeight = (int) round($height * $scale);

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}
