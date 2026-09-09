<?php

namespace App\Enums;

/**
 * The moments in a live class's lifecycle a reminder can fire for.
 *
 * Each type fires at most once per class; `LiveClassReminderLog` is the guard.
 */
enum LiveClassReminderType: string
{
    case Scheduled = 'scheduled';
    case DayBefore = 'day_before';
    case HourBefore = 'hour_before';
    case StartingSoon = 'starting_soon';
    case LiveNow = 'live_now';
    case RecordingAvailable = 'recording_available';
}
