<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Owns lesson completion and the derived course completion percentage.
 */
class ProgressService
{
    public function __construct(
        private readonly CertificateService $certificates,
    ) {}

    /**
     * Mark a lesson complete for a student.
     *
     * The unique (user_id, lesson_id) index plus `updateOrCreate` keeps a
     * repeated submission idempotent instead of creating a second row.
     */
    public function markComplete(User $user, Lesson $lesson): LessonProgress
    {
        $progress = LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'course_id' => $lesson->course_id,
                'is_completed' => true,
                'completed_at' => now(),
                'last_viewed_at' => now(),
            ],
        );

        $this->syncCourseProgress($user, $lesson->loadMissing('course')->course);

        return $progress;
    }

    public function markIncomplete(User $user, Lesson $lesson): LessonProgress
    {
        $progress = LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'course_id' => $lesson->course_id,
                'is_completed' => false,
                'completed_at' => null,
                'last_viewed_at' => now(),
            ],
        );

        $this->syncCourseProgress($user, $lesson->loadMissing('course')->course);

        return $progress;
    }

    /**
     * Record how much of a video lesson has been watched.
     *
     * Percentages only ever move forward, so scrubbing backwards through a
     * video cannot reduce a student's recorded progress.
     */
    public function recordWatchProgress(User $user, Lesson $lesson, int $seconds, int $percent): LessonProgress
    {
        $percent = max(0, min(100, $percent));

        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $progress->course_id = $lesson->course_id;
        $progress->watched_seconds = max($progress->watched_seconds ?? 0, $seconds);
        $progress->watched_percent = max($progress->watched_percent ?? 0, $percent);
        $progress->last_viewed_at = now();

        $threshold = (int) config('academy.video_completion_percent', 90);
        $reachedEnd = $progress->watched_percent >= $threshold;

        if ($reachedEnd && ! $progress->is_completed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        if ($reachedEnd) {
            $this->syncCourseProgress($user, $lesson->loadMissing('course')->course);
        }

        return $progress;
    }

    /**
     * Note that a student opened a lesson, without changing completion.
     */
    public function touchViewed(User $user, Lesson $lesson): void
    {
        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['course_id' => $lesson->course_id, 'last_viewed_at' => now()],
        );
    }

    /**
     * Completion percentage over every published lesson in the course.
     */
    public function percentFor(User $user, Course $course): int
    {
        $total = $course->lessons()->where('is_published', true)->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->completedLessonCount($user, $course);

        return (int) floor(($completed / $total) * 100);
    }

    public function completedLessonCount(User $user, Course $course): int
    {
        return LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->whereHas('lesson', fn ($query) => $query->where('is_published', true))
            ->count();
    }

    /**
     * Completed lesson IDs for one course, keyed for fast sidebar lookups.
     *
     * @return array<int, bool>
     */
    public function completedLessonMap(User $user, Course $course): array
    {
        return LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->flip()
            ->map(fn (): bool => true)
            ->all();
    }

    /**
     * Recalculate the cached percentage on the enrolment and, once the course
     * threshold is met, complete the enrolment and issue the certificate.
     */
    public function syncCourseProgress(User $user, Course $course): ?Enrollment
    {
        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment === null) {
            return null;
        }

        $percent = $this->percentFor($user, $course);
        $threshold = $course->completion_threshold ?: (int) config('academy.certificate_threshold', 100);

        DB::transaction(function () use ($enrollment, $percent, $threshold): void {
            $enrollment->progress_percent = $percent;

            if ($percent >= $threshold && $enrollment->status === EnrollmentStatus::Active) {
                $enrollment->status = EnrollmentStatus::Completed;
                $enrollment->completed_at = now();
            }

            // Dropping below the threshold reopens the course but never revokes
            // a certificate that has already been issued.
            if ($percent < $threshold && $enrollment->status === EnrollmentStatus::Completed) {
                $enrollment->status = EnrollmentStatus::Active;
                $enrollment->completed_at = null;
            }

            $enrollment->save();
        });

        if ($enrollment->isCompleted()) {
            $this->certificates->issueIfEligible($user, $course);
        }

        return $enrollment->refresh();
    }
}
