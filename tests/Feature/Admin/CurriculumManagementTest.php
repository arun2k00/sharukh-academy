<?php

namespace Tests\Feature\Admin;

use App\Enums\LessonType;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LiveClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_module_is_added_to_the_end_of_the_course(): void
    {
        $course = Course::factory()->create();
        CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);

        $this->actingAs($this->admin())
            ->post(route('admin.modules.store', $course), ['title' => 'Structure that stays readable'])
            ->assertRedirect(route('admin.courses.curriculum', $course));

        $module = CourseModule::where('title', 'Structure that stays readable')->firstOrFail();

        $this->assertSame(2, $module->sort_order);
    }

    public function test_modules_can_be_reordered(): void
    {
        $course = Course::factory()->create();
        $first = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        $second = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

        $this->actingAs($this->admin())
            ->patch(route('admin.modules.move', $second), ['direction' => 'up'])
            ->assertRedirect();

        $this->assertSame(1, $second->fresh()->sort_order);
        $this->assertSame(2, $first->fresh()->sort_order);
    }

    public function test_moving_the_first_module_up_changes_nothing(): void
    {
        $course = Course::factory()->create();
        $first = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 1]);
        $second = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 2]);

        $this->actingAs($this->admin())->patch(route('admin.modules.move', $first), ['direction' => 'up']);

        $this->assertSame(1, $first->fresh()->sort_order);
        $this->assertSame(2, $second->fresh()->sort_order);
    }

    public function test_reordering_repairs_duplicate_positions(): void
    {
        $course = Course::factory()->create();
        $a = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 5]);
        $b = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 5]);
        $c = CourseModule::factory()->create(['course_id' => $course->id, 'sort_order' => 5]);

        $this->actingAs($this->admin())->patch(route('admin.modules.move', $c), ['direction' => 'up']);

        $this->assertSame([1, 2, 3], [
            $a->fresh()->sort_order,
            $c->fresh()->sort_order,
            $b->fresh()->sort_order,
        ]);
    }

    public function test_deleting_a_module_removes_its_lessons(): void
    {
        $course = $this->courseWithCurriculum(modules: 2, lessonsPerModule: 2);
        $module = CourseModule::where('course_id', $course->id)->orderBy('sort_order')->firstOrFail();

        $this->actingAs($this->admin())
            ->delete(route('admin.modules.destroy', $module))
            ->assertRedirect(route('admin.courses.curriculum', $course));

        $this->assertDatabaseMissing('course_modules', ['id' => $module->id]);
        $this->assertDatabaseCount('lessons', 2);
    }

    public function test_a_video_lesson_is_created_and_the_course_length_recalculated(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $course), [
                'course_module_id' => $module->id,
                'title' => 'Contribution margin, honestly calculated',
                'type' => 'video',
                'video_url' => 'https://youtu.be/M7lc1UVf-VE',
                'duration_minutes' => 21,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.courses.curriculum', $course));

        $lesson = Lesson::firstOrFail();

        $this->assertSame('M7lc1UVf-VE', $lesson->video_id);
        $this->assertSame(LessonType::Video, $lesson->type);
        $this->assertSame(21, $course->fresh()->duration_minutes);
    }

    public function test_a_video_lesson_requires_a_video_link(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $course), [
                'course_module_id' => $module->id,
                'title' => 'Missing its video',
                'type' => 'video',
                'duration_minutes' => 10,
            ])
            ->assertSessionHasErrors('video_url');
    }

    public function test_a_text_lesson_requires_content(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $course), [
                'course_module_id' => $module->id,
                'title' => 'Empty reading',
                'type' => 'text',
                'duration_minutes' => 10,
            ])
            ->assertSessionHasErrors('content');
    }

    public function test_a_lesson_cannot_be_filed_under_another_courses_module(): void
    {
        $course = Course::factory()->create();
        $otherModule = CourseModule::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $course), [
                'course_module_id' => $otherModule->id,
                'title' => 'Wrong course',
                'type' => 'video',
                'video_url' => 'https://youtu.be/M7lc1UVf-VE',
                'duration_minutes' => 5,
            ])
            ->assertSessionHasErrors('course_module_id');
    }

    public function test_lesson_slugs_are_unique_within_a_course_but_may_repeat_across_courses(): void
    {
        $courseOne = Course::factory()->create();
        $moduleOne = CourseModule::factory()->create(['course_id' => $courseOne->id]);
        Lesson::factory()->create([
            'course_id' => $courseOne->id,
            'course_module_id' => $moduleOne->id,
            'slug' => 'welcome',
        ]);

        $payload = [
            'course_module_id' => $moduleOne->id,
            'title' => 'Welcome',
            'type' => 'video',
            'video_url' => 'https://youtu.be/M7lc1UVf-VE',
            'duration_minutes' => 5,
        ];

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $courseOne), $payload)
            ->assertSessionHasErrors('slug');

        $courseTwo = Course::factory()->create();
        $moduleTwo = CourseModule::factory()->create(['course_id' => $courseTwo->id]);

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $courseTwo), [...$payload, 'course_module_id' => $moduleTwo->id])
            ->assertSessionHasNoErrors();
    }

    public function test_switching_a_lesson_away_from_video_clears_the_stale_video_id(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.lessons.update', $lesson), [
                'course_module_id' => $module->id,
                'title' => $lesson->title,
                'slug' => $lesson->slug,
                'type' => 'text',
                'content' => 'Now a reading instead.',
                'duration_minutes' => 8,
                'is_published' => '1',
            ])
            ->assertRedirect();

        $lesson->refresh();

        $this->assertNull($lesson->video_id);
        $this->assertNull($lesson->video_url);
    }

    public function test_a_live_lesson_must_reference_a_class_from_the_same_course(): void
    {
        $course = Course::factory()->create();
        $module = CourseModule::factory()->create(['course_id' => $course->id]);
        $foreignClass = LiveClass::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.lessons.store', $course), [
                'course_module_id' => $module->id,
                'title' => 'Live session',
                'type' => 'live',
                'live_class_id' => $foreignClass->id,
                'duration_minutes' => 60,
            ])
            ->assertSessionHasErrors('live_class_id');
    }

    public function test_lessons_can_be_reordered_inside_a_module(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 3);
        $lessons = Lesson::where('course_id', $course->id)->orderBy('sort_order')->get();

        $this->actingAs($this->admin())
            ->patch(route('admin.lessons.move', $lessons[2]), ['direction' => 'up'])
            ->assertRedirect();

        $this->assertSame(2, $lessons[2]->fresh()->sort_order);
        $this->assertSame(3, $lessons[1]->fresh()->sort_order);
    }

    public function test_the_move_direction_is_validated(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 2);
        $lesson = $this->firstLesson($course);

        $this->actingAs($this->admin())
            ->patch(route('admin.lessons.move', $lesson), ['direction' => 'sideways'])
            ->assertSessionHasErrors('direction');
    }
}
