<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use App\Services\EnrollmentService;

class LessonPolicy
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {}

    /**
     * A lesson is viewable when it is a free preview of a published course, or
     * when the viewer holds an access-granting enrolment.
     */
    public function view(?User $user, Lesson $lesson): bool
    {
        if ($user?->isAdmin() === true) {
            return true;
        }

        $course = $lesson->loadMissing('course')->course;

        if (! $course->isPublished() || ! $lesson->is_published) {
            return false;
        }

        if ($lesson->isFreePreview()) {
            return true;
        }

        return $user !== null && $this->enrollments->hasAccess($user, $course);
    }

    /**
     * Only an enrolled student records progress; previews stay stateless.
     */
    public function trackProgress(User $user, Lesson $lesson): bool
    {
        return $lesson->is_published
            && $this->enrollments->hasAccess($user, $lesson->loadMissing('course')->course);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin();
    }
}
