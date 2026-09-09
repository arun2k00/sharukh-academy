<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Parses and builds YouTube URLs.
 *
 * Only the extracted video ID is ever persisted, so a malformed or hostile URL
 * cannot be echoed back into an iframe `src` attribute.
 */
final class YouTube
{
    /**
     * Hosts accepted when extracting a video ID.
     *
     * @var list<string>
     */
    private const ALLOWED_HOSTS = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'music.youtube.com',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com',
        'youtu.be',
        'www.youtu.be',
    ];

    /**
     * Extract the 11 character video ID from any accepted YouTube URL.
     *
     * Accepts watch, share, embed, shorts and live URL shapes, plus a bare ID.
     */
    public static function id(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (self::isVideoId($value)) {
            return $value;
        }

        // Tolerate URLs pasted without a scheme (youtu.be/xyz).
        if (! Str::startsWith($value, ['http://', 'https://'])) {
            $value = 'https://'.ltrim($value, '/');
        }

        $parts = parse_url($value);

        if ($parts === false || ! isset($parts['host'])) {
            return null;
        }

        if (! in_array(mb_strtolower($parts['host']), self::ALLOWED_HOSTS, true)) {
            return null;
        }

        parse_str($parts['query'] ?? '', $query);

        if (isset($query['v']) && self::isVideoId((string) $query['v'])) {
            return (string) $query['v'];
        }

        $segments = array_values(array_filter(explode('/', $parts['path'] ?? '')));

        // youtu.be/<id>, /embed/<id>, /shorts/<id>, /live/<id>, /v/<id>
        $candidate = match (true) {
            $segments === [] => null,
            in_array($segments[0], ['embed', 'shorts', 'live', 'v'], true) => $segments[1] ?? null,
            default => $segments[0],
        };

        return $candidate !== null && self::isVideoId($candidate) ? $candidate : null;
    }

    /**
     * Whether the given value can be resolved to a YouTube video.
     */
    public static function isValid(?string $value): bool
    {
        return self::id($value) !== null;
    }

    /**
     * Privacy-preserving embed URL for an already-extracted video ID.
     *
     * @param  array<string, int|string>  $params
     */
    public static function embedUrl(?string $videoId, array $params = []): ?string
    {
        if (! self::isVideoId((string) $videoId)) {
            return null;
        }

        $params = array_merge([
            'rel' => 0,
            'modestbranding' => 1,
            'playsinline' => 1,
            'enablejsapi' => 1,
            'origin' => rtrim((string) config('app.url'), '/'),
        ], $params);

        return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?'.http_build_query($params);
    }

    /**
     * Canonical watch URL, used when an admin needs to open the source video.
     */
    public static function watchUrl(?string $videoId): ?string
    {
        return self::isVideoId((string) $videoId)
            ? 'https://www.youtube.com/watch?v='.$videoId
            : null;
    }

    public static function thumbnailUrl(?string $videoId, string $quality = 'hqdefault'): ?string
    {
        return self::isVideoId((string) $videoId)
            ? 'https://i.ytimg.com/vi/'.$videoId.'/'.$quality.'.jpg'
            : null;
    }

    private static function isVideoId(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1;
    }
}
