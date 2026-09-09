<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Draws the placeholder course thumbnails used by the demonstration catalogue.
 *
 * These are deliberately typographic rather than stock photography: an academy
 * replacing them with real artwork gets an obvious improvement, and until then
 * the catalogue still looks composed instead of broken.
 */
final class DemoThumbnailFactory
{
    /**
     * A shared stock photo used as a temporary thumbnail and banner for every
     * course until real per-course photography exists — see public/bannerheader.jpg.
     */
    public static function stockPhotoPath(): ?string
    {
        $source = public_path('bannerheader.jpg');

        if (! is_file($source)) {
            return null;
        }

        $path = 'courses/stock-photo.jpg';

        Storage::disk('public')->put($path, file_get_contents($source));

        return $path;
    }

    /**
     * Background and accent per category slug.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const PALETTE = [
        'marketing' => ['#1b4331', '#d8e8df'],
        'design' => ['#1a1a18', '#ece7db'],
        'business' => ['#6f4d26', '#f2e6cf'],
        'analytics' => ['#102a1f', '#b2cfbe'],
        'data-engineering' => ['#0f1f33', '#bcd4f0'],
    ];

    /**
     * Write a 16:9 thumbnail and return its path on the public disk.
     */
    public static function make(string $courseSlug, string $title, string $categorySlug, string $level): string
    {
        [$background, $accent] = self::PALETTE[$categorySlug] ?? self::PALETTE['design'];

        $lines = self::wrapTitle($title);
        $startY = 330 - (count($lines) - 1) * 34;
        $titleMarkup = '';

        foreach ($lines as $index => $line) {
            $titleMarkup .= sprintf(
                '<text x="80" y="%d" fill="#ffffff" font-family="Georgia, serif" font-size="68">%s</text>',
                $startY + ($index * 82),
                htmlspecialchars($line, ENT_XML1),
            );
        }

        $svg = strtr(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1280 720" width="1280" height="720" role="img">'
            .'<rect width="1280" height="720" fill="@bg"/>'
            .'<rect x="40" y="40" width="1200" height="640" fill="none" stroke="@accent" stroke-opacity="0.25"/>'
            .'<line x1="80" y1="176" x2="1200" y2="176" stroke="@accent" stroke-opacity="0.3"/>'
            .'<line x1="80" y1="600" x2="1200" y2="600" stroke="@accent" stroke-opacity="0.3"/>'
            .'<text x="80" y="132" fill="@accent" font-family="Helvetica, Arial, sans-serif" font-size="22" letter-spacing="5">@category</text>'
            .'@title'
            .'<text x="80" y="646" fill="@accent" font-family="Helvetica, Arial, sans-serif" font-size="21" letter-spacing="3.5">EDUNOVA</text>'
            .'<text x="1200" y="646" fill="@accent" fill-opacity="0.75" text-anchor="end" font-family="Helvetica, Arial, sans-serif" font-size="21">@level</text>'
            .'</svg>',
            [
                '@bg' => $background,
                '@accent' => $accent,
                '@category' => htmlspecialchars(Str::upper($categorySlug), ENT_XML1),
                '@title' => $titleMarkup,
                '@level' => htmlspecialchars(Str::title($level), ENT_XML1),
            ],
        );

        $path = 'courses/'.$courseSlug.'.svg';

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * Write a wide banner-format header image for the course's own page and
     * return its path on the public disk.
     */
    public static function makeBanner(string $courseSlug, string $title, string $categorySlug): string
    {
        [$background, $accent] = self::PALETTE[$categorySlug] ?? self::PALETTE['design'];

        $svg = strtr(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1500 500" width="1500" height="500" role="img">'
            .'<rect width="1500" height="500" fill="@bg"/>'
            .'<rect x="60" y="60" width="1380" height="380" fill="none" stroke="@accent" stroke-opacity="0.25"/>'
            .'<text x="120" y="225" fill="@accent" font-family="Helvetica, Arial, sans-serif" font-size="24" letter-spacing="5">@category</text>'
            .'<text x="118" y="305" fill="#ffffff" font-family="Georgia, serif" font-size="54">@title</text>'
            .'<text x="120" y="400" fill="@accent" fill-opacity="0.75" font-family="Helvetica, Arial, sans-serif" font-size="21" letter-spacing="3.5">EDUNOVA</text>'
            .'</svg>',
            [
                '@bg' => $background,
                '@accent' => $accent,
                '@category' => htmlspecialchars(Str::upper($categorySlug), ENT_XML1),
                '@title' => htmlspecialchars(Str::limit($title, 46), ENT_XML1),
            ],
        );

        $path = 'courses/banners/'.$courseSlug.'.svg';

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * Break the title into lines short enough to sit inside the artwork.
     *
     * @return list<string>
     */
    private static function wrapTitle(string $title): array
    {
        return array_slice(explode("\n", wordwrap($title, 18, "\n", false)), 0, 4);
    }
}
