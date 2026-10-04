<?php

namespace Tests\Feature\Tutor;

use App\Enums\LessonType;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_trainer_adds_a_module_to_their_own_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);

        $this->actingAs($trainer)
            ->post(route('tutor.modules.store', $course), ['title' => 'Structure that stays readable'])
            ->assertRedirect(route('tutor.courses.curriculum', $course));

        $module = CourseModule::where('title', 'Structure that stays readable')->firstOrFail();

        $this->assertSame(2, $module->sort_order);
    }

    public function test_modules_can_be_reordered(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $first = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        $second = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

        $this->actingAs($trainer)
            ->patch(route('tutor.modules.move', $second), ['direction' => 'up'])
            ->assertRedirect();

        $this->assertSame(1, $second->fresh()->sort_order);
        $this->assertSame(2, $first->fresh()->sort_order);
    }

    public function test_deleting_a_module_removes_its_lessons(): void
    {
        $trainer = $this->trainer();
        $course = $this->courseWithCurriculum(['instructor_id' => $trainer->instructor->id], modules: 2, lessonsPerModule: 2);
        $module = CourseModule::where('course_id', $course->id)->orderBy('sort_order')->firstOrFail();

        $this->actingAs($trainer)
            ->delete(route('tutor.modules.destroy', $module))
            ->assertRedirect(route('tutor.courses.curriculum', $course));

        $this->assertDatabaseMissing('course_modules', ['id' => $module->id]);
        $this->assertDatabaseCount('lessons', 2);
    }

    public function test_a_video_lesson_is_created_and_the_course_length_recalculated(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $module = CourseModule::factory()->create(['course_id' => $course->id]);

        $this->actingAs($trainer)
            ->post(route('tutor.lessons.store', $course), [
                'course_module_id' => $module->id,
                'title' => 'Contribution margin, honestly calculated',
                'type' => 'video',
                'video_url' => 'https://youtu.be/M7lc1UVf-VE',
                'duration_minutes' => 21,
                'is_published' => '1',
            ])
            ->assertRedirect(route('tutor.courses.curriculum', $course));

        $lesson = Lesson::firstOrFail();

        $this->assertSame('M7lc1UVf-VE', $lesson->video_id);
        $this->assertSame(LessonType::Video, $lesson->type);
        $this->assertSame(21, $course->fresh()->duration_minutes);
    }

    public function test_a_video_lesson_still_requires_a_video_link(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $module = CourseModule::factory()->create(['course_id' => $course->id]);

        $this->actingAs($trainer)
            ->post(route('tutor.lessons.store', $course), [
                'course_module_id' => $module->id,
                'title' => 'Missing its video',
                'type' => 'video',
                'duration_minutes' => 10,
            ])
            ->assertSessionHasErrors('video_url');
    }

    public function test_lessons_can_be_reordered_inside_a_module(): void
    {
        $trainer = $this->trainer();
        $course = $this->courseWithCurriculum(['instructor_id' => $trainer->instructor->id], modules: 1, lessonsPerModule: 3);
        $lessons = Lesson::where('course_id', $course->id)->orderBy('sort_order')->get();

        $this->actingAs($trainer)
            ->patch(route('tutor.lessons.move', $lessons[2]), ['direction' => 'up'])
            ->assertRedirect();

        $this->assertSame(2, $lessons[2]->fresh()->sort_order);
        $this->assertSame(3, $lessons[1]->fresh()->sort_order);
    }
}
