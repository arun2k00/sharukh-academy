<?php

namespace Tests;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * A published course with a real curriculum behind it.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function courseWithCurriculum(
        array $attributes = [],
        int $modules = 2,
        int $lessonsPerModule = 2,
    ): Course {
        $course = Course::factory()->create($attributes);

        for ($moduleIndex = 1; $moduleIndex <= $modules; $moduleIndex++) {
            $module = CourseModule::factory()->create([
                'course_id' => $course->id,
                'sort_order' => $moduleIndex,
            ]);

            for ($lessonIndex = 1; $lessonIndex <= $lessonsPerModule; $lessonIndex++) {
                Lesson::factory()->create([
                    'course_id' => $course->id,
                    'course_module_id' => $module->id,
                    'sort_order' => $lessonIndex,
                ]);
            }
        }

        return $course->fresh();
    }

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function student(): User
    {
        return User::factory()->create();
    }

    /**
     * The first lesson of a course in curriculum order.
     */
    protected function firstLesson(Course $course): Lesson
    {
        return Lesson::query()
            ->where('lessons.course_id', $course->id)
            ->inCurriculumOrder()
            ->firstOrFail();
    }

    /**
     * A draft course, useful for checking the catalogue hides it.
     */
    protected function draftCourse(): Course
    {
        return Course::factory()->draft()->create();
    }

    protected function publishedCourse(): Course
    {
        return Course::factory()->create(['status' => CourseStatus::Published]);
    }

    protected function assertIsAdmin(User $user): void
    {
        $this->assertSame(UserRole::Admin, $user->role);
    }
}
