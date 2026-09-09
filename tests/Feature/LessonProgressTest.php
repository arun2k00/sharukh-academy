<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CurriculumService;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_a_lesson_complete_records_progress_and_advances(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);
        $next = app(CurriculumService::class)->nextLesson($course, $lesson);

        $this->actingAs($student)
            ->post(route('lessons.complete', [$course, $lesson]))
            ->assertRedirect(route('learn.lesson', [$course, $next]));

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
        ]);
    }

    public function test_completing_the_same_lesson_twice_does_not_create_a_second_record(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);

        $this->actingAs($student)->post(route('lessons.complete', [$course, $lesson]));
        $this->actingAs($student)->post(route('lessons.complete', [$course, $lesson]));

        $this->assertSame(1, LessonProgress::query()
            ->where('user_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->count());
    }

    public function test_the_course_percentage_reflects_completed_lessons(): void
    {
        $course = $this->courseWithCurriculum(modules: 2, lessonsPerModule: 2);
        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lessons = Lesson::where('course_id', $course->id)->orderBy('id')->get();
        $progress = app(ProgressService::class);

        $progress->markComplete($student, $lessons[0]);
        $this->assertSame(25, $enrollment->fresh()->progress_percent);

        $progress->markComplete($student, $lessons[1]);
        $this->assertSame(50, $enrollment->fresh()->progress_percent);
    }

    public function test_unpublished_lessons_are_excluded_from_the_percentage(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 4);
        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lessons = Lesson::where('course_id', $course->id)->orderBy('id')->get();
        $lessons->last()->forceFill(['is_published' => false])->save();

        $progress = app(ProgressService::class);
        $progress->markComplete($student, $lessons[0]);

        // Three published lessons remain, so one of them is 33%.
        $this->assertSame(33, $enrollment->fresh()->progress_percent);
    }

    public function test_uncompleting_a_lesson_reduces_the_percentage(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 2);
        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);

        $this->actingAs($student)->post(route('lessons.complete', [$course, $lesson]));
        $this->assertSame(50, $enrollment->fresh()->progress_percent);

        $this->actingAs($student)->delete(route('lessons.uncomplete', [$course, $lesson]));
        $this->assertSame(0, $enrollment->fresh()->progress_percent);
    }

    public function test_a_student_without_an_enrolment_cannot_record_progress(): void
    {
        $course = $this->courseWithCurriculum();
        $lesson = $this->firstLesson($course);

        $this->actingAs($this->student())
            ->post(route('lessons.complete', [$course, $lesson]))
            ->assertForbidden();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_watch_progress_only_ever_moves_forward(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);

        $this->actingAs($student)
            ->postJson(route('lessons.watch', [$course, $lesson]), ['seconds' => 300, 'percent' => 60])
            ->assertOk()
            ->assertJsonPath('watched_percent', 60);

        // Scrubbing back to the start must not reduce the recorded position.
        $this->actingAs($student)
            ->postJson(route('lessons.watch', [$course, $lesson]), ['seconds' => 10, 'percent' => 2])
            ->assertOk()
            ->assertJsonPath('watched_percent', 60);
    }

    public function test_reaching_the_end_of_a_video_completes_the_lesson(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);

        $this->actingAs($student)
            ->postJson(route('lessons.watch', [$course, $lesson]), ['seconds' => 900, 'percent' => 95])
            ->assertOk()
            ->assertJsonPath('completed', true);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
        ]);
    }

    public function test_watch_progress_rejects_out_of_range_values(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->postJson(route('lessons.watch', [$course, $this->firstLesson($course)]), [
                'seconds' => -5,
                'percent' => 500,
            ])
            ->assertStatus(422);
    }

    public function test_one_student_cannot_affect_another_students_progress(): void
    {
        $course = $this->courseWithCurriculum();
        $first = $this->student();
        $second = User::factory()->create();

        Enrollment::factory()->create(['user_id' => $first->id, 'course_id' => $course->id]);
        Enrollment::factory()->create(['user_id' => $second->id, 'course_id' => $course->id]);

        $lesson = $this->firstLesson($course);

        $this->actingAs($first)->post(route('lessons.complete', [$course, $lesson]));

        $this->assertDatabaseMissing('lesson_progress', [
            'user_id' => $second->id,
            'lesson_id' => $lesson->id,
        ]);
    }
}
