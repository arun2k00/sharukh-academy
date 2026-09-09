<?php

namespace App\Support;

/**
 * Compares an uploaded image's aspect ratio against the academy's
 * recommended certificate format (A4 landscape) so the admin can be warned
 * without the upload being blocked outright.
 */
final class ImageRatio
{
    /**
     * A4 landscape: 297mm x 210mm.
     */
    public const RECOMMENDED_RATIO = 297 / 210;

    public const RECOMMENDED_LABEL = 'A4 landscape (297 × 210mm, ratio 1.414:1)';

    /**
     * Minimum long-edge pixel width considered print-quality (roughly 200dpi
     * at A4 landscape width).
     */
    public const MIN_RECOMMENDED_WIDTH = 2000;

    public const TOLERANCE = 0.06;

    public static function matchesRecommended(int $width, int $height): bool
    {
        if ($height === 0) {
            return false;
        }

        return abs(($width / $height) - self::RECOMMENDED_RATIO) <= self::TOLERANCE;
    }

    public static function isHighResolution(int $width, int $height): bool
    {
        return max($width, $height) >= self::MIN_RECOMMENDED_WIDTH;
    }

    /**
     * A short human warning when the upload deviates from the recommendation,
     * or null when it is within tolerance.
     */
    public static function warningFor(int $width, int $height): ?string
    {
        $warnings = [];

        if (! self::matchesRecommended($width, $height)) {
            $ratio = $height > 0 ? round($width / $height, 3) : 0;
            $warnings[] = "This image's ratio is {$ratio}:1, which does not match the recommended ".self::RECOMMENDED_LABEL.'. It will still be used, but text positions may need more adjustment.';
        }

        if (! self::isHighResolution($width, $height)) {
            $warnings[] = 'This image is lower resolution than recommended ('.self::MIN_RECOMMENDED_WIDTH.'px on the long edge). Certificates may look soft when printed.';
        }

        return $warnings === [] ? null : implode(' ', $warnings);
    }
}
