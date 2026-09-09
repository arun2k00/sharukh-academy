<?php

namespace App\Services;

use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Creates and maintains the single enrolment record per student and course.
 */
class EnrollmentService
{
    public function __construct(
        private readonly ProgressService $progress,
        private readonly NotificationDispatcher $notifications,
    ) {}

    /**
     * Grant access to a course.
     *
     * A student who previously cancelled or expired is reactivated rather than
     * given a second row, which the unique (user_id, course_id) index forbids.
     */
    public function enroll(
        User $user,
        Course $course,
        EnrollmentSource $source = EnrollmentSource::Admin,
        ?Order $order = null,
    ): Enrollment {
        return DB::transaction(function () use ($user, $course, $source, $order): Enrollment {
            $enrollment = Enrollment::firstOrNew([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            if (! $enrollment->exists) {
                $enrolledAt = now();

                $enrollment->fill([
                    'status' => EnrollmentStatus::Active,
                    'source' => $source,
                    'enrolled_at' => $enrolledAt,
                    'progress_percent' => 0,
                    // The course's access policy (§9) decides how long this
                    // grant lasts; lifetime access leaves this null.
                    'expires_at' => $course->computeAccessExpiry($enrolledAt),
                ]);
            }

            if (! $enrollment->grantsAccess()) {
                $enrollment->status = EnrollmentStatus::Active;
                $enrollment->expires_at = null;
                $enrollment->completed_at = null;
            }

            if ($order !== null) {
                $enrollment->order_id = $order->id;
            }

            $isNewGrant = ! $enrollment->exists || $enrollment->isDirty('status');

            $enrollment->save();

            if ($isNewGrant) {
                $this->notifications->enrollmentConfirmed($user, $course);
            }

            return $enrollment;
        });
    }

    public function find(User $user, Course $course): ?Enrollment
    {
        return Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
    }

    /**
     * The enrolment that unlocks a course, or null when access is not granted.
     */
    public function accessFor(User $user, Course $course): ?Enrollment
    {
        $enrollment = $this->find($user, $course);

        return $enrollment?->grantsAccess() === true ? $enrollment : null;
    }

    public function hasAccess(User $user, Course $course): bool
    {
        return $this->accessFor($user, $course) !== null;
    }

    /**
     * Change an enrolment's lifecycle status from the admin panel.
     */
    public function changeStatus(Enrollment $enrollment, EnrollmentStatus $status): Enrollment
    {
        $enrollment->status = $status;

        $enrollment->completed_at = match ($status) {
            EnrollmentStatus::Completed => $enrollment->completed_at ?? now(),
            EnrollmentStatus::Active => null,
            default => $enrollment->completed_at,
        };

        $enrollment->save();

        return $enrollment;
    }

    /**
     * Remember where the student was, so "Continue learning" is accurate.
     */
    public function recordAccess(Enrollment $enrollment, Lesson $lesson): void
    {
        $enrollment->forceFill([
            'last_lesson_id' => $lesson->id,
            'last_accessed_at' => now(),
        ])->save();
    }
}
