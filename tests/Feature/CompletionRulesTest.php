<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\CourseCompletionMode;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use App\Services\CertificateService;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Course completion rules (§11): certificate eligibility must use the
 * course's configured rule, never just "some lessons were completed".
 */
class CompletionRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_lessons_only_mode_ignores_optional_lessons_in_the_percentage(): void
    {
        $course = $this->courseWithCurriculum(['completion_mode' => CourseCompletionMode::RequiredLessonsOnly], modules: 1, lessonsPerModule: 4);
        $lessons = Lesson::where('course_id', $course->id)->orderBy('id')->get();

        // Mark two of the four lessons optional.
        $lessons[2]->forceFill(['is_required' => false])->save();
        $lessons[3]->forceFill(['is_required' => false])->save();

        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $progress = app(ProgressService::class);
        $progress->markComplete($student, $lessons[0]);
        $progress->markComplete($student, $lessons[1]);

        // Both required lessons done — 100%, even though two optional lessons
        // were never touched.
        $this->assertSame(100, $enrollment->fresh()->progress_percent);
        $this->assertDatabaseHas('certificates', ['user_id' => $student->id, 'course_id' => $course->id]);
    }

    public function test_all_lessons_mode_still_requires_every_published_lesson(): void
    {
        $course = $this->courseWithCurriculum(['completion_mode' => CourseCompletionMode::AllLessons], modules: 1, lessonsPerModule: 4);
        $lessons = Lesson::where('course_id', $course->id)->orderBy('id')->get();
        $lessons[2]->forceFill(['is_required' => false])->save();

        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $progress = app(ProgressService::class);
        $progress->markComplete($student, $lessons[0]);
        $progress->markComplete($student, $lessons[1]);
        $progress->markComplete($student, $lessons[3]);

        // The "optional" flag is ignored under this mode — lesson 2 still
        // counts, so three of four is 75%, not complete.
        $this->assertSame(75, $enrollment->fresh()->progress_percent);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_a_course_requiring_live_attendance_withholds_the_certificate_until_attendance_is_marked(): void
    {
        $course = $this->courseWithCurriculum(
            ['completion_requires_live_attendance' => true],
            modules: 1,
            lessonsPerModule: 1,
        );

        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'scheduled_at' => now()->subWeek(),
        ]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        app(ProgressService::class)->markComplete($student, $this->firstLesson($course));

        // Every lesson is done, but attendance at the past class is missing.
        $this->assertDatabaseCount('certificates', 0);
        $this->assertFalse(app(CertificateService::class)->isEligible($student, $course));

        LiveClassAttendance::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $student->id,
            'status' => AttendanceStatus::Present,
            'marked_at' => now(),
        ]);

        $this->assertTrue(app(CertificateService::class)->isEligible($student, $course));

        app(CertificateService::class)->issueIfEligible($student, $course);
        $this->assertDatabaseHas('certificates', ['user_id' => $student->id, 'course_id' => $course->id]);
    }

    public function test_marking_attendance_absent_does_not_satisfy_the_requirement(): void
    {
        $course = $this->courseWithCurriculum(
            ['completion_requires_live_attendance' => true],
            modules: 1,
            lessonsPerModule: 1,
        );

        LiveClass::factory()->create(['course_id' => $course->id, 'scheduled_at' => now()->subDay()]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);
        app(ProgressService::class)->markComplete($student, $this->firstLesson($course));

        $this->assertFalse(app(CertificateService::class)->isEligible($student, $course));
    }

    public function test_a_future_live_class_does_not_block_the_certificate(): void
    {
        $course = $this->courseWithCurriculum(
            ['completion_requires_live_attendance' => true],
            modules: 1,
            lessonsPerModule: 1,
        );

        LiveClass::factory()->create(['course_id' => $course->id, 'scheduled_at' => now()->addWeek()]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);
        app(ProgressService::class)->markComplete($student, $this->firstLesson($course));

        $this->assertTrue(app(CertificateService::class)->isEligible($student, $course));
    }
}
