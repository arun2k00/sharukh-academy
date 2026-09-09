<?php

namespace Database\Seeders\Support;

use GdImage;
use Illuminate\Support\Facades\Storage;

/**
 * Draws a simple, elegant A4-landscape certificate background with GD so the
 * demo certificate template has real artwork to composite onto — the same
 * renderer a real academy's uploaded design would use.
 */
final class DemoCertificateBackgroundFactory
{
    private const WIDTH = 2400;

    private const HEIGHT = 1697; // A4 landscape ratio, 1.414:1

    public static function make(string $path): void
    {
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        $cream = imagecolorallocate($image, 252, 251, 248);
        $ink900 = imagecolorallocate($image, 26, 26, 24);
        $pine700 = imagecolorallocate($image, 27, 67, 49);
        $sand300 = imagecolorallocate($image, 222, 214, 196);

        imagefill($image, 0, 0, $cream);

        // Outer and inner rule lines, echoing the plain certificate's frame.
        $margin = 70;
        imagesetthickness($image, 3);
        imagerectangle($image, $margin, $margin, self::WIDTH - $margin, self::HEIGHT - $margin, $ink900);
        imagesetthickness($image, 1);
        imagerectangle($image, $margin + 22, $margin + 22, self::WIDTH - $margin - 22, self::HEIGHT - $margin - 22, $sand300);

        // A restrained pine accent bar at the top, and a rule above the footer.
        imagefilledrectangle($image, $margin + 22, $margin + 22, self::WIDTH - $margin - 22, $margin + 34, $pine700);
        imageline($image, self::WIDTH * 0.22, self::HEIGHT * 0.78, self::WIDTH * 0.78, self::HEIGHT * 0.78, $sand300);

        $font = resource_path('fonts/certificates/DejaVuSans.ttf');
        $label = 'CERTIFICATE OF COMPLETION';
        $box = imagettfbbox(30, 0, $font, $label);
        $textWidth = abs($box[2] - $box[0]);
        imagettftext($image, 30, 0, (int) ((self::WIDTH - $textWidth) / 2), (int) (self::HEIGHT * 0.22), $ink900, $font, $label);

        self::writeTo($image, $path);
        imagedestroy($image);
    }

    private static function writeTo(GdImage $image, string $path): void
    {
        ob_start();
        imagepng($image, null, 6);
        $bytes = (string) ob_get_clean();

        Storage::disk('public')->put($path, $bytes);
    }

    public static function width(): int
    {
        return self::WIDTH;
    }

    public static function height(): int
    {
        return self::HEIGHT;
    }
}
