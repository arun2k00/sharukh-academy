<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Builds a minimal RFC 5545 calendar file for one event.
 *
 * Deliberately does not include the Zoom URL — only a link back to the live
 * class page, so a downloaded or forwarded .ics file never leaks the meeting
 * itself (§6).
 */
final class IcsCalendar
{
    public static function forEvent(
        string $uid,
        string $summary,
        string $description,
        CarbonInterface $start,
        CarbonInterface $end,
        string $url,
    ): string {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//'.self::escape(config('academy.name')).'//LMS//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.self::escape($uid),
            'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:'.$start->clone()->utc()->format('Ymd\THis\Z'),
            'DTEND:'.$end->clone()->utc()->format('Ymd\THis\Z'),
            'SUMMARY:'.self::escape($summary),
            'DESCRIPTION:'.self::escape($description),
            'URL:'.self::escape($url),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        // Lines must be CRLF-terminated per the spec.
        return implode("\r\n", $lines)."\r\n";
    }

    private static function escape(string $value): string
    {
        return str_replace(
            ['\\', ',', ';', "\n"],
            ['\\\\', '\\,', '\;', '\\n'],
            $value,
        );
    }
}
