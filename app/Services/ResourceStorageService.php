<?php

namespace App\Services;

use App\Support\WebpImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores lesson and live-class attachments.
 *
 * Files go to the private disk under an unguessable generated name; they are
 * only ever served by a controller that has checked the student's enrolment.
 */
class ResourceStorageService
{
    /**
     * @return array{file_path: string, original_name: string, mime_type: string|null, extension: string, file_size: int}
     */
    public function store(UploadedFile $file, string $directory): array
    {
        $path = $file->store($directory, 'local');

        return [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'extension' => mb_strtolower($file->getClientOriginalExtension()),
            'file_size' => $file->getSize() ?: 0,
        ];
    }

    public function delete(?string $path): void
    {
        if (filled($path) && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    /**
     * Store a public image (course thumbnail, avatar) and return its path.
     * Re-encoded to WebP on the way in; see WebpImage.
     */
    public function storeImage(UploadedFile $file, string $directory): string
    {
        return WebpImage::store($file, $directory);
    }

    public function deleteImage(?string $path): void
    {
        if (filled($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
