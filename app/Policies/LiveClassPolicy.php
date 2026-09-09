<?php

namespace App\Policies;

use App\Models\LiveClass;
use App\Models\User;
use App\Services\EnrollmentService;

class LiveClassPolicy
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {}

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Details are visible to students enrolled in the parent course.
     */
    public function view(User $user, LiveClass $liveClass): bool
    {
        return $user->isAdmin()
            || $this->enrollments->hasAccess($user, $liveClass->loadMissing('course')->course);
    }

    /**
     * The Zoom link is only handed out inside the join window, and only to
     * students who may view the class.
     */
    public function join(User $user, LiveClass $liveClass): bool
    {
        return $this->view($user, $liveClass) && $liveClass->isJoinable();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, LiveClass $liveClass): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LiveClass $liveClass): bool
    {
        return $user->isAdmin();
    }
}
