<?php

namespace App\Services\Certificates;

use App\Models\CertificateTemplate;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use GdImage;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Composites a certificate template's background artwork with a student's
 * details, using GD and a small set of bundled, redistributable fonts.
 *
 * The same render() call backs the admin's live preview, the student's
 * on-screen certificate, the downloadable PNG, and the social-share image —
 * so what an admin previews is pixel-identical to what a student receives.
 */
class CertificateImageService
{
    /**
     * @var array<string, string>
     */
    private const FONT_FILES = [
        'serif' => 'DejaVuSerif.ttf',
        'serif-bold' => 'DejaVuSerif-Bold.ttf',
        'serif-italic' => 'DejaVuSerif-Italic.ttf',
        'sans' => 'DejaVuSans.ttf',
        'sans-bold' => 'DejaVuSans-Bold.ttf',
    ];

    /**
     * Fraction of the shorter image dimension the QR code occupies when shown.
     */
    private const QR_SIZE_RATIO = 0.09;

    /**
     * Render a template with the given field values as raw PNG bytes.
     *
     * @param  array<string, string|null>  $data  Keys matching
     *                                            CertificateTemplate field names, plus an optional `verification_url`
     *                                            used only when the template has its QR code enabled.
     */
    public function render(CertificateTemplate $template, array $data): string
    {
        $image = $this->loadBackground($template);

        foreach ($template->fields as $key => $config) {
            $value = trim((string) ($data[$key] ?? ''));

            if ($value !== '') {
                $this->drawText($image, $config, $value);
            }
        }

        if ($template->signature_path && $template->signature_x !== null) {
            $this->overlaySignature($image, $template);
        }

        if ($template->show_qr_code && filled($data['verification_url'] ?? null)) {
            $this->overlayQrCode($image, $data['verification_url']);
        }

        ob_start();
        imagepng($image, null, 6);
        $bytes = (string) ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    /**
     * Sample data used for the admin's "preview with sample data" screen,
     * matching the worked example in the certificate template specification.
     *
     * @return array<string, string>
     */
    public function sampleData(): array
    {
        return [
            'student_name' => 'John Smith',
            'course_name' => 'Digital Marketing Masterclass',
            'completion_date' => now()->format('j F Y'),
            'certificate_id' => 'ACA-2026-000123',
            'instructor_name' => 'Sample Instructor',
            'academy_name' => config('academy.name'),
            'verification_url' => url('/verify-certificate/ACA-2026-000123'),
        ];
    }

    private function loadBackground(CertificateTemplate $template): GdImage
    {
        $path = Storage::disk('public')->path($template->image_path);
        $extension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'png' => @imagecreatefrompng($path),
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            default => false,
        };

        if (! $image instanceof GdImage) {
            throw new RuntimeException("Could not read the certificate template image at [{$path}].");
        }

        imagesavealpha($image, true);

        return $image;
    }

    /**
     * @param  array{x: float|int, y: float|int, font?: string, font_size?: int, color?: string, align?: string}  $config
     */
    private function drawText(GdImage $image, array $config, string $text): void
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $fontKey = $config['font'] ?? 'serif';
        $fontFile = $this->fontPath($fontKey);
        $fontSize = max(1, (int) ($config['font_size'] ?? 36));
        $align = $config['align'] ?? 'center';
        $color = $this->allocateColor($image, (string) ($config['color'] ?? '#1a1a18'));

        // The anchor is stored as a percentage of the image so the layout
        // survives the background artwork being replaced at a different
        // resolution, then clamped so a stray value cannot land off-canvas.
        $anchorX = $width * (max(0.0, min(100.0, (float) $config['x'])) / 100);
        $anchorY = $height * (max(0.0, min(100.0, (float) $config['y'])) / 100);

        $box = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = abs($box[2] - $box[0]);
        $textHeight = abs($box[7] - $box[1]);

        $x = match ($align) {
            'left' => $anchorX,
            'right' => $anchorX - $textWidth,
            default => $anchorX - ($textWidth / 2),
        };

        // imagettftext positions the *baseline*; nudge up by roughly half the
        // glyph height so the anchor reads as the visual centre of the text.
        $baselineY = $anchorY + ($textHeight / 2);

        imagettftext($image, $fontSize, 0, (int) round($x), (int) round($baselineY), $color, $fontFile, $text);
    }

    private function overlaySignature(GdImage $image, CertificateTemplate $template): void
    {
        $path = Storage::disk('public')->path((string) $template->signature_path);

        if (! is_file($path)) {
            return;
        }

        $extension = mb_strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $signature = match ($extension) {
            'png' => @imagecreatefrompng($path),
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            default => false,
        };

        if (! $signature instanceof GdImage) {
            return;
        }

        $canvasWidth = imagesx($image);
        $canvasHeight = imagesy($image);

        $targetWidth = $canvasWidth * ((float) $template->signature_width / 100);
        $ratio = imagesy($signature) / max(1, imagesx($signature));
        $targetHeight = $targetWidth * $ratio;

        $destX = ($canvasWidth * ((float) $template->signature_x / 100)) - ($targetWidth / 2);
        $destY = ($canvasHeight * ((float) $template->signature_y / 100)) - ($targetHeight / 2);

        imagealphablending($image, true);
        imagecopyresampled(
            $image, $signature,
            (int) round($destX), (int) round($destY), 0, 0,
            (int) round($targetWidth), (int) round($targetHeight),
            imagesx($signature), imagesy($signature),
        );
        imagedestroy($signature);
    }

    private function overlayQrCode(GdImage $image, string $url): void
    {
        $canvasWidth = imagesx($image);
        $canvasHeight = imagesy($image);
        $size = (int) round(min($canvasWidth, $canvasHeight) * self::QR_SIZE_RATIO);
        $margin = (int) round($size * 0.25);

        $result = Builder::create()
            ->writer(new PngWriter)
            ->data($url)
            ->size($size)
            ->margin(4)
            ->build();

        $qr = @imagecreatefromstring($result->getString());

        if (! $qr instanceof GdImage) {
            return;
        }

        $destX = $canvasWidth - $size - $margin;
        $destY = $canvasHeight - $size - $margin;

        imagecopy($image, $qr, $destX, $destY, 0, 0, imagesx($qr), imagesy($qr));
        imagedestroy($qr);
    }

    private function allocateColor(GdImage $image, string $hex): int
    {
        $hex = ltrim($hex, '#');

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            $hex = '1a1a18';
        }

        [$r, $g, $b] = array_map(fn (string $part): int => hexdec($part), str_split($hex, 2));

        return imagecolorallocate($image, $r, $g, $b);
    }

    private function fontPath(string $key): string
    {
        $file = self::FONT_FILES[$key] ?? self::FONT_FILES['serif'];

        return resource_path('fonts/certificates/'.$file);
    }

    /**
     * @return list<string>
     */
    public static function availableFonts(): array
    {
        return array_keys(self::FONT_FILES);
    }
}
