<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Services\EnrollmentService;

class CoursePolicy
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {}

    /**
     * Anyone may browse the catalogue.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * The public course page is visible while the course is published; staff
     * can preview drafts.
     */
    public function view(?User $user, Course $course): bool
    {
        return $course->isPublished() || $user?->isAdmin() === true;
    }

    /**
     * Access to the learning area behind /learn.
     */
    public function learn(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $course->isPublished() && $this->enrollments->hasAccess($user, $course);
    }

    /**
     * Whether the student may enrol themselves right now.
     */
    public function enroll(User $user, Course $course): bool
    {
        return $course->isPublished() && ! $this->enrollments->hasAccess($user, $course);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }
}
