<?php

namespace App\Services\Certificates;

use App\Models\CertificateTemplate;
use App\Support\ImageRatio;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles the upload side of certificate templates: storing the artwork,
 * reading its real pixel dimensions, and shaping the validated field
 * positions into the compact array the image renderer expects.
 */
class CertificateTemplateService
{
    /**
     * @param  array<string, mixed>  $validated
     * @return array{path: string, width: int, height: int, warning: string|null}
     */
    public function storeImage(UploadedFile $file): array
    {
        [$width, $height] = getimagesize($file->getRealPath()) ?: [0, 0];
        $path = $file->store('certificate-templates', 'public');

        return [
            'path' => $path,
            'width' => $width,
            'height' => $height,
            'warning' => ImageRatio::warningFor($width, $height),
        ];
    }

    public function storeSignature(UploadedFile $file): string
    {
        return $file->store('certificate-templates/signatures', 'public');
    }

    public function deleteImage(?string $path): void
    {
        if (filled($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Build the `fields` column from validated request input: required
     * fields are always kept, optional fields only when their "enabled"
     * checkbox was ticked.
     *
     * @param  array<string, array<string, mixed>>  $submitted
     * @return array<string, array{x: float, y: float, font: string, font_size: int, color: string, align: string}>
     */
    public function buildFieldConfig(array $submitted): array
    {
        $fields = [];

        foreach (CertificateTemplate::REQUIRED_FIELDS as $key) {
            $fields[$key] = $this->normaliseField($submitted[$key]);
        }

        foreach (CertificateTemplate::OPTIONAL_FIELDS as $key) {
            $entry = $submitted[$key] ?? null;

            if ($entry !== null && ($entry['enabled'] ?? false)) {
                $fields[$key] = $this->normaliseField($entry);
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array{x: float, y: float, font: string, font_size: int, color: string, align: string}
     */
    private function normaliseField(array $field): array
    {
        return [
            'x' => round((float) $field['x'], 2),
            'y' => round((float) $field['y'], 2),
            'font' => (string) $field['font'],
            'font_size' => (int) $field['font_size'],
            'color' => (string) $field['color'],
            'align' => (string) $field['align'],
        ];
    }
}
