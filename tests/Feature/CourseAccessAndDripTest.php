<?php

namespace Tests\Feature;

use App\Enums\CourseAccessType;
use App\Enums\ModuleUnlockType;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\CurriculumService;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Course access policy (§9) and content drip (§10): both are enforced at
 * enrolment/lesson-resolution time server-side, never only in the UI.
 */
class CourseAccessAndDripTest extends TestCase
{
    use RefreshDatabase;

    public function test_lifetime_access_never_expires(): void
    {
        $course = Course::factory()->create(['access_type' => CourseAccessType::Lifetime]);
        $enrollment = app(EnrollmentService::class)->enroll($this->student(), $course);

        $this->assertNull($enrollment->expires_at);
    }

    public function test_limited_duration_access_expires_n_days_after_enrolment(): void
    {
        $course = Course::factory()->create([
            'access_type' => CourseAccessType::LimitedDuration,
            'access_duration_days' => 30,
        ]);

        $enrollment = app(EnrollmentService::class)->enroll($this->student(), $course);

        $this->assertNotNull($enrollment->expires_at);
        $this->assertTrue($enrollment->expires_at->isSameDay(now()->addDays(30)));
    }

    public function test_fixed_expiry_access_expires_on_the_configured_date(): void
    {
        $course = Course::factory()->create([
            'access_type' => CourseAccessType::FixedExpiry,
            'access_fixed_expiry_date' => now()->addMonths(2)->toDateString(),
        ]);

        $enrollment = app(EnrollmentService::class)->enroll($this->student(), $course);

        $this->assertTrue($enrollment->expires_at->isSameDay(now()->addMonths(2)));
    }

    public function test_an_expired_limited_duration_enrolment_blocks_learning_but_keeps_history(): void
    {
        $course = $this->courseWithCurriculum(['access_type' => CourseAccessType::LimitedDuration, 'access_duration_days' => 1]);
        $student = $this->student();

        $enrollment = Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now()->subDays(5),
            'expires_at' => now()->subDays(4),
        ]);

        $this->actingAs($student)
            ->get(route('learn.course', $course))
            ->assertForbidden();

        // The enrolment record itself survives — this is history, not a delete.
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }

    public function test_a_lesson_in_an_immediately_unlocked_module_is_reachable(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->get(route('learn.lesson', [$course, $this->firstLesson($course)]))
            ->assertOk();
    }

    public function test_a_module_locked_for_days_after_enrolment_blocks_its_lessons(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create([
            'course_id' => $course->id,
            'unlock_type' => ModuleUnlockType::DaysAfterEnrollment,
            'unlock_after_days' => 7,
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
        ]);

        $student = $this->student();
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now()->subDays(2),
        ]);

        $this->actingAs($student)
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertForbidden();
    }

    public function test_a_module_unlocks_once_enough_days_have_passed(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create([
            'course_id' => $course->id,
            'unlock_type' => ModuleUnlockType::DaysAfterEnrollment,
            'unlock_after_days' => 7,
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
        ]);

        $student = $this->student();
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now()->subDays(8),
        ]);

        $this->actingAs($student)
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertOk();
    }

    public function test_a_module_locked_to_a_fixed_future_date_blocks_its_lessons(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create([
            'course_id' => $course->id,
            'unlock_type' => ModuleUnlockType::FixedDate,
            'unlock_at' => now()->addWeek()->toDateString(),
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
        ]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertForbidden();
    }

    public function test_a_drip_locked_lesson_cannot_have_progress_recorded_directly(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create([
            'course_id' => $course->id,
            'unlock_type' => ModuleUnlockType::FixedDate,
            'unlock_at' => now()->addWeek()->toDateString(),
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
        ]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->post(route('lessons.complete', [$course, $lesson]))
            ->assertForbidden();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_navigation_skips_a_lesson_locked_by_drip(): void
    {
        $course = Course::factory()->create();
        $openModule = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        $lockedModule = CourseModule::factory()->create([
            'course_id' => $course->id,
            'sort_order' => 2,
            'unlock_type' => ModuleUnlockType::DaysAfterEnrollment,
            'unlock_after_days' => 30,
        ]);
        $finalModule = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 3]);

        $first = Lesson::factory()->create(['course_id' => $course->id, 'course_module_id' => $openModule->id, 'sort_order' => 1]);
        Lesson::factory()->create(['course_id' => $course->id, 'course_module_id' => $lockedModule->id, 'sort_order' => 1]);
        $final = Lesson::factory()->create(['course_id' => $course->id, 'course_module_id' => $finalModule->id, 'sort_order' => 1]);

        $student = $this->student();
        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        $next = app(CurriculumService::class)->nextLesson(
            $course,
            $first,
            Enrollment::where('user_id', $student->id)->first(),
        );

        $this->assertTrue($next->is($final));
    }
}
