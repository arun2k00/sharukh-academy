<?php

namespace App\Support;

final class Duration
{
    /**
     * Render a minute count as a compact human label: 0m, 45m, 2h, 6h 20m.
     */
    public static function label(?int $minutes): string
    {
        $minutes = max(0, (int) $minutes);

        if ($minutes < 60) {
            return $minutes.'m';
        }

        $hours = intdiv($minutes, 60);
        $remainder = $minutes % 60;

        return $remainder === 0 ? $hours.'h' : $hours.'h '.$remainder.'m';
    }

    /**
     * Long-form label used on course detail pages: "6 hours 20 minutes".
     */
    public static function longLabel(?int $minutes): string
    {
        $minutes = max(0, (int) $minutes);
        $hours = intdiv($minutes, 60);
        $remainder = $minutes % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours.' '.str('hour')->plural($hours);
        }

        if ($remainder > 0 || $parts === []) {
            $parts[] = $remainder.' '.str('minute')->plural($remainder);
        }

        return implode(' ', $parts);
    }
}
