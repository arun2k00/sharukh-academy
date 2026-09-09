<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Resolves curriculum ordering and lesson-to-lesson navigation.
 */
class CurriculumService
{
    /**
     * Every published lesson of a course in curriculum order.
     *
     * @return Collection<int, Lesson>
     */
    public function orderedLessons(Course $course, bool $publishedOnly = true): Collection
    {
        return Lesson::query()
            ->where('lessons.course_id', $course->id)
            ->when($publishedOnly, fn ($query) => $query->where('lessons.is_published', true))
            ->inCurriculumOrder()
            ->get();
    }

    public function firstLesson(Course $course): ?Lesson
    {
        return $this->orderedLessons($course)->first();
    }

    public function nextLesson(Course $course, Lesson $current): ?Lesson
    {
        return $this->neighbour($course, $current, 1);
    }

    public function previousLesson(Course $course, Lesson $current): ?Lesson
    {
        return $this->neighbour($course, $current, -1);
    }

    /**
     * Where a returning student should land: the lesson they last opened, or
     * failing that the first lesson they have not completed yet.
     */
    public function resumeLesson(User $user, Course $course, ?Enrollment $enrollment = null): ?Lesson
    {
        $enrollment ??= Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $lessons = $this->orderedLessons($course);

        if ($lessons->isEmpty()) {
            return null;
        }

        if ($enrollment?->last_lesson_id !== null) {
            $last = $lessons->firstWhere('id', $enrollment->last_lesson_id);

            if ($last !== null) {
                return $last;
            }
        }

        $completedIds = $user->lessonProgress()
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->all();

        return $lessons->first(fn (Lesson $lesson): bool => ! in_array($lesson->id, $completedIds, true))
            ?? $lessons->first();
    }

    private function neighbour(Course $course, Lesson $current, int $offset): ?Lesson
    {
        $lessons = $this->orderedLessons($course);
        $index = $lessons->search(fn (Lesson $lesson): bool => $lesson->id === $current->id);

        if ($index === false) {
            return null;
        }

        return $lessons->get($index + $offset);
    }
}
