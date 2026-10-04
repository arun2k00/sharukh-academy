<?php

namespace Tests\Feature\Tutor;

use App\Enums\EnrollmentSource;
use App\Models\Course;
use App\Models\LiveClass;
use App\Notifications\AcademyNotification;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LiveClassManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(Course $course, array $overrides = []): array
    {
        return array_merge([
            'course_id' => $course->id,
            'title' => 'Live: reviewing your funnel',
            'scheduled_at' => now()->addWeek()->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ], $overrides);
    }

    public function test_a_trainer_schedules_a_live_class_for_their_own_course(): void
    {
        Notification::fake();

        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);

        $this->actingAs($trainer)
            ->post(route('tutor.live-classes.store'), $this->validPayload($course))
            ->assertRedirect();

        $liveClass = LiveClass::firstOrFail();

        $this->assertSame($course->id, $liveClass->course_id);
        $this->assertSame($trainer->instructor->id, $liveClass->instructor_id);
    }

    public function test_a_trainer_cannot_schedule_a_live_class_for_a_course_they_do_not_own(): void
    {
        $trainer = $this->trainer();
        $someoneElsesCourse = Course::factory()->create();

        $this->actingAs($trainer)
            ->post(route('tutor.live-classes.store'), $this->validPayload($someoneElsesCourse))
            ->assertSessionHasErrors('course_id');

        $this->assertDatabaseCount('live_classes', 0);
    }

    public function test_enrolled_students_are_notified_when_a_live_class_is_scheduled(): void
    {
        Notification::fake();

        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $student = $this->student();
        app(EnrollmentService::class)->enroll($student, $course, EnrollmentSource::Free);

        $this->actingAs($trainer)->post(route('tutor.live-classes.store'), $this->validPayload($course));

        Notification::assertSentTo($student, AcademyNotification::class);
    }

    public function test_a_trainer_updates_their_own_live_class(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'instructor_id' => $trainer->instructor->id,
        ]);

        $this->actingAs($trainer)
            ->put(route('tutor.live-classes.update', $liveClass), $this->validPayload($course, [
                'title' => 'A renamed session',
                'slug' => $liveClass->slug,
            ]))
            ->assertRedirect(route('tutor.live-classes.edit', $liveClass));

        $this->assertSame('A renamed session', $liveClass->fresh()->title);
    }

    public function test_a_trainer_removes_their_own_live_class(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'instructor_id' => $trainer->instructor->id,
        ]);

        $this->actingAs($trainer)
            ->delete(route('tutor.live-classes.destroy', $liveClass))
            ->assertRedirect(route('tutor.live-classes.index'));

        $this->assertSoftDeleted('live_classes', ['id' => $liveClass->id]);
    }
}
