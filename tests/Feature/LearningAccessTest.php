<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Server-side enforcement of who may open course content.
 */
class LearningAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_sent_to_the_login_screen(): void
    {
        $course = $this->courseWithCurriculum();

        $this->get(route('learn.course', $course))->assertRedirect(route('login'));
    }

    public function test_a_student_without_an_enrolment_is_refused(): void
    {
        $course = $this->courseWithCurriculum();
        $lesson = $this->firstLesson($course);

        $this->actingAs($this->student())
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertForbidden();
    }

    public function test_an_enrolled_student_can_open_a_lesson(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        $lesson = $this->firstLesson($course);

        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertOk()
            ->assertSee($lesson->title);
    }

    public function test_a_cancelled_enrolment_no_longer_grants_access(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        Enrollment::factory()->cancelled()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($student)
            ->get(route('learn.course', $course))
            ->assertForbidden();
    }

    public function test_an_expired_enrolment_no_longer_grants_access_even_while_marked_active(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAs($student)
            ->get(route('learn.course', $course))
            ->assertForbidden();
    }

    public function test_a_student_cannot_open_a_lesson_belonging_to_another_course(): void
    {
        $enrolledCourse = $this->courseWithCurriculum();
        $otherCourse = $this->courseWithCurriculum();
        $student = $this->student();

        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $enrolledCourse->id]);

        $foreignLesson = $this->firstLesson($otherCourse);

        // Scoped bindings resolve the lesson through the course, so a lesson
        // from elsewhere simply does not exist on this route.
        $this->actingAs($student)
            ->get(route('learn.lesson', [$enrolledCourse, $foreignLesson]))
            ->assertNotFound();
    }

    public function test_an_unpublished_lesson_is_not_reachable(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);
        $lesson->forceFill(['is_published' => false])->save();

        $this->actingAs($student)
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertNotFound();
    }

    public function test_entering_a_course_redirects_to_the_resume_point(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->get(route('learn.course', $course))
            ->assertRedirect(route('learn.lesson', [$course, $this->firstLesson($course)]));
    }

    public function test_a_free_preview_lesson_is_public_but_other_lessons_are_not(): void
    {
        $course = $this->courseWithCurriculum();
        $lessons = Lesson::where('course_id', $course->id)->orderBy('id')->get();

        $preview = $lessons->first();
        $preview->forceFill(['is_preview' => true])->save();

        $locked = $lessons->last();

        $this->get(route('courses.preview', [$course, $preview]))->assertOk();
        $this->get(route('courses.preview', [$course, $locked]))->assertNotFound();
    }

    public function test_a_draft_course_is_hidden_from_the_public_catalogue(): void
    {
        $draft = $this->draftCourse();

        $this->get(route('courses.index'))->assertOk()->assertDontSee($draft->title);
        $this->get(route('courses.show', $draft))->assertForbidden();
    }

    public function test_an_administrator_may_preview_a_draft_course(): void
    {
        $draft = $this->draftCourse();

        $this->actingAs($this->admin())
            ->get(route('courses.show', $draft))
            ->assertOk();
    }
}
