<?php

namespace Tests\Feature;

use App\Enums\LiveClassReminderType;
use App\Enums\LiveClassStatus;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\LiveClassReminderLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The scheduled reminder sweep (§7) — cron-compatible, idempotent, and
 * resilient to one bad row not stopping the rest of the run.
 */
class LiveClassReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    private function enrolledStudent(int $courseId): User
    {
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $courseId]);

        return $student;
    }

    public function test_a_day_before_reminder_is_sent_and_logged(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->enrolledStudent($course->id);
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id, 'scheduled_at' => now()->addDay()]);

        $this->artisan('academy:send-reminders')->assertSuccessful();

        $this->assertSame(1, $student->fresh()->notifications()->count());
        $this->assertDatabaseHas('live_class_reminder_logs', [
            'live_class_id' => $liveClass->id,
            'type' => LiveClassReminderType::DayBefore->value,
        ]);
    }

    public function test_running_the_command_twice_never_sends_a_duplicate(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->enrolledStudent($course->id);
        LiveClass::factory()->create(['course_id' => $course->id, 'scheduled_at' => now()->addDay()]);

        $this->artisan('academy:send-reminders');
        $this->artisan('academy:send-reminders');

        $this->assertSame(1, $student->fresh()->notifications()->count());
        $this->assertSame(1, LiveClassReminderLog::count());
    }

    public function test_the_hour_before_and_day_before_reminders_are_independent(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->enrolledStudent($course->id);
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id, 'scheduled_at' => now()->addDay()]);

        $this->artisan('academy:send-reminders');
        $this->assertSame(1, $student->fresh()->notifications()->count());

        // Travel forward to the hour-before window; the day-before reminder
        // must not fire again, but the hour-before one now should.
        $this->travelTo(now()->addDay()->subHour());
        $this->artisan('academy:send-reminders');

        $this->assertSame(2, $student->fresh()->notifications()->count());
        $this->assertDatabaseHas('live_class_reminder_logs', [
            'live_class_id' => $liveClass->id, 'type' => LiveClassReminderType::HourBefore->value,
        ]);
    }

    public function test_a_cancelled_class_receives_no_reminders(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->enrolledStudent($course->id);
        LiveClass::factory()->create([
            'course_id' => $course->id,
            'scheduled_at' => now()->addDay(),
            'status' => LiveClassStatus::Cancelled,
        ]);

        $this->artisan('academy:send-reminders');

        $this->assertSame(0, $student->fresh()->notifications()->count());
    }

    public function test_a_class_with_no_enrolled_students_sends_nothing_but_still_succeeds(): void
    {
        $course = $this->courseWithCurriculum();
        LiveClass::factory()->create(['course_id' => $course->id, 'scheduled_at' => now()->addDay()]);

        $this->artisan('academy:send-reminders')->assertSuccessful();

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_an_enrolment_expiring_in_a_week_gets_one_notice_not_repeated(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        $enrollment = Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'expires_at' => now()->addDays(7),
        ]);

        $this->artisan('academy:send-reminders');
        $this->artisan('academy:send-reminders');

        $this->assertSame(1, $student->fresh()->notifications()->count());
    }

    public function test_an_enrolment_expiring_far_in_the_future_gets_no_notice_yet(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'expires_at' => now()->addDays(60),
        ]);

        $this->artisan('academy:send-reminders');

        $this->assertSame(0, $student->fresh()->notifications()->count());
    }
}
