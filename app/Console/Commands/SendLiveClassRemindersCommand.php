<?php

namespace App\Console\Commands;

use App\Enums\EnrollmentStatus;
use App\Enums\LiveClassReminderType;
use App\Enums\LiveClassStatus;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\LiveClassReminderLog;
use App\Services\NotificationDispatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * The one scheduled task the whole notification system depends on (§7, §13).
 *
 * Designed to run every 5–15 minutes from ordinary cron — see DEPLOYMENT.md —
 * with no permanent worker required. Every send is guarded by
 * `LiveClassReminderLog`'s unique (live_class_id, type) index, so running the
 * command twice in the same window, or recovering from a missed run, never
 * produces a duplicate notification.
 *
 * A failure sending one class's reminder is caught and logged rather than
 * aborting the run, so one bad row cannot silently stop every other
 * notification for the day (§7: "must continue functioning even if optional
 * notifications are temporarily unavailable").
 */
class SendLiveClassRemindersCommand extends Command
{
    protected $signature = 'academy:send-reminders';

    protected $description = 'Send live-class reminders and access-expiry notices that have come due';

    public function handle(NotificationDispatcher $notifications): int
    {
        $sent = 0;
        $sent += $this->sendLiveClassReminders($notifications);
        $sent += $this->sendAccessExpiringNotices($notifications);

        $this->info("Sent {$sent} notification batch(es).");

        return self::SUCCESS;
    }

    private function sendLiveClassReminders(NotificationDispatcher $notifications): int
    {
        $sent = 0;

        $upcoming = LiveClass::query()
            ->whereNot('status', LiveClassStatus::Cancelled)
            ->where('scheduled_at', '>=', now()->subHours(6))
            ->where('scheduled_at', '<=', now()->addDays(2))
            ->get();

        foreach ($upcoming as $liveClass) {
            // A list of [type, moment] pairs rather than an enum-keyed array —
            // PHP array keys must be int|string, not enum instances.
            $windows = [
                [LiveClassReminderType::DayBefore, $liveClass->scheduled_at->copy()->subDay()],
                [LiveClassReminderType::HourBefore, $liveClass->scheduled_at->copy()->subHour()],
                [LiveClassReminderType::StartingSoon, $liveClass->scheduled_at->copy()
                    ->subMinutes((int) config('academy.live_join_window', 15))],
                [LiveClassReminderType::LiveNow, $liveClass->scheduled_at],
            ];

            foreach ($windows as [$type, $triggerAt]) {
                // Due once the trigger moment has passed, but only within a
                // generous catch-up window so a reminder for a moment long
                // gone is never sent late.
                if (! now()->betweenIncluded($triggerAt, $triggerAt->copy()->addMinutes(20))) {
                    continue;
                }

                if (LiveClassReminderLog::where('live_class_id', $liveClass->id)->where('type', $type)->exists()) {
                    continue;
                }

                try {
                    $notifications->liveClassReminder($liveClass, $type);
                    LiveClassReminderLog::create([
                        'live_class_id' => $liveClass->id,
                        'type' => $type,
                        'sent_at' => now(),
                    ]);
                    $sent++;
                } catch (\Throwable $exception) {
                    Log::error('Live class reminder failed', [
                        'live_class_id' => $liveClass->id,
                        'type' => $type->value,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }
        }

        return $sent;
    }

    /**
     * Once-only notice roughly a week before a limited-duration or
     * fixed-expiry enrolment ends.
     */
    private function sendAccessExpiringNotices(NotificationDispatcher $notifications): int
    {
        $sent = 0;

        $expiringSoon = Enrollment::query()
            ->where('status', EnrollmentStatus::Active)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now()->addDays(6), now()->addDays(8)])
            ->with(['user', 'course'])
            ->get();

        foreach ($expiringSoon as $enrollment) {
            if ($notifications->hasSentAccessExpiringNotice($enrollment)) {
                continue;
            }

            try {
                $notifications->accessExpiringSoon($enrollment, (int) round(now()->diffInDays($enrollment->expires_at)));
                $sent++;
            } catch (\Throwable $exception) {
                Log::error('Access-expiring notice failed', [
                    'enrollment_id' => $enrollment->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
