<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Draws placeholder instructor portraits so the faculty page never falls
 * back to a bare initial in a large empty box. Deliberately typographic,
 * matching DemoThumbnailFactory's course artwork — an instructor replacing
 * this with a real photo gets an obvious improvement, and until then the
 * page still looks composed.
 */
final class DemoAvatarFactory
{
    private const WIDTH = 800;

    private const HEIGHT = 1000;

    /**
     * Background and initials colour per instructor slug, cycling through a
     * small set of tones so a faculty grid does not look monotonous.
     *
     * @var list<array{0: string, 1: string}>
     */
    private const PALETTE = [
        ['#0b3d24', '#7fe3ab'],
        ['#0f1f33', '#9dc4f2'],
        ['#1a1a18', '#ece7db'],
        ['#3a2413', '#f2c98f'],
    ];

    public static function make(string $instructorSlug, string $name, int $paletteIndex): string
    {
        [$background, $accent] = self::PALETTE[$paletteIndex % count(self::PALETTE)];

        $initials = self::initials($name);

        $svg = strtr(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000" width="800" height="1000" role="img">'
            .'<rect width="800" height="1000" fill="@bg"/>'
            .'<rect x="36" y="36" width="728" height="928" fill="none" stroke="@accent" stroke-opacity="0.3"/>'
            .'<text x="400" y="560" fill="@accent" font-family="Georgia, serif" font-size="220" text-anchor="middle">@initials</text>'
            .'</svg>',
            [
                '@bg' => $background,
                '@accent' => $accent,
                '@initials' => htmlspecialchars($initials, ENT_XML1),
            ],
        );

        $path = 'instructors/'.$instructorSlug.'.svg';

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    private static function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];

        $letters = array_map(
            fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)),
            array_filter($words),
        );

        return implode('', array_slice($letters, 0, 2)) ?: '—';
    }
}
