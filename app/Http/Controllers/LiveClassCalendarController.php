<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Support\IcsCalendar;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * "Add to Calendar" (§6): one .ics file that Google Calendar, Apple Calendar
 * and Outlook can all import directly.
 */
class LiveClassCalendarController extends Controller
{
    public function __invoke(LiveClass $liveClass): Response
    {
        Gate::authorize('view', $liveClass);

        $liveClass->loadMissing('course');

        $ics = IcsCalendar::forEvent(
            uid: 'live-class-'.$liveClass->id.'@'.parse_url(config('app.url'), PHP_URL_HOST),
            summary: $liveClass->title.' — '.$liveClass->course->title,
            description: ($liveClass->description ?: 'Live class for '.$liveClass->course->title)
                .' Join details are on your dashboard: '.route('live-classes.show', $liveClass),
            start: $liveClass->scheduled_at,
            end: $liveClass->ends_at,
            url: route('live-classes.show', $liveClass),
        );

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.str($liveClass->slug)->limit(60, '').'.ics"',
        ]);
    }
}
