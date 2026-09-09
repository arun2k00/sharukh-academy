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
     * Every published lesson of a course in curriculum order, regardless of
     * content-drip state. Used for the full outline display.
     *
     * @return Collection<int, Lesson>
     */
    public function orderedLessons(Course $course, bool $publishedOnly = true): Collection
    {
        return Lesson::query()
            ->where('lessons.course_id', $course->id)
            ->when($publishedOnly, fn ($query) => $query->where('lessons.is_published', true))
            ->inCurriculumOrder()
            ->with('module')
            ->get();
    }

    /**
     * The subset of lessons a student may actually be routed to right now:
     * every ordered lesson whose module has unlocked for their enrolment.
     *
     * A null enrolment (e.g. an admin previewing) skips drip filtering
     * entirely — server-side authorization is still enforced separately by
     * `LessonPolicy`, so this only ever affects *where navigation points*,
     * never what is actually reachable.
     *
     * @return Collection<int, Lesson>
     */
    public function accessibleLessons(Course $course, ?Enrollment $enrollment): Collection
    {
        $lessons = $this->orderedLessons($course);

        if ($enrollment === null) {
            return $lessons;
        }

        return $lessons
            ->filter(fn (Lesson $lesson): bool => $lesson->module === null
                || $lesson->module->isUnlockedFor($enrollment->enrolled_at))
            ->values();
    }

    public function firstLesson(Course $course): ?Lesson
    {
        return $this->orderedLessons($course)->first();
    }

    public function nextLesson(Course $course, Lesson $current, ?Enrollment $enrollment = null): ?Lesson
    {
        return $this->neighbour($this->accessibleLessons($course, $enrollment), $current, 1);
    }

    public function previousLesson(Course $course, Lesson $current, ?Enrollment $enrollment = null): ?Lesson
    {
        return $this->neighbour($this->accessibleLessons($course, $enrollment), $current, -1);
    }

    /**
     * Where a returning student should land: the lesson they last opened (if
     * it is still unlocked), or failing that the first unlocked lesson they
     * have not completed yet.
     */
    public function resumeLesson(User $user, Course $course, ?Enrollment $enrollment = null): ?Lesson
    {
        $enrollment ??= Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $lessons = $this->accessibleLessons($course, $enrollment);

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

    /**
     * @param  Collection<int, Lesson>  $lessons
     */
    private function neighbour(Collection $lessons, Lesson $current, int $offset): ?Lesson
    {
        $index = $lessons->search(fn (Lesson $lesson): bool => $lesson->id === $current->id);

        if ($index === false) {
            return null;
        }

        return $lessons->get($index + $offset);
    }
}
