<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {}

    /**
     * A lesson is viewable when it is a free preview of a published course,
     * or when the viewer holds an access-granting enrolment AND the lesson's
     * module has unlocked for them under the course's content-drip schedule.
     */
    public function view(?User $user, Lesson $lesson): Response
    {
        if ($user?->isAdmin() === true) {
            return Response::allow();
        }

        $course = $lesson->loadMissing('course')->course;

        if (! $course->isPublished() || ! $lesson->is_published) {
            // An unpublished lesson reads as absent, not merely forbidden —
            // it should not confirm to a visitor that it exists at all.
            return Response::denyAsNotFound('This lesson is not available.');
        }

        if ($lesson->isFreePreview()) {
            return Response::allow();
        }

        if ($user === null) {
            return Response::deny('Sign in to access this lesson.');
        }

        $enrollment = $this->enrollments->accessFor($user, $course);

        if ($enrollment === null) {
            return Response::deny('You are not enrolled in this course.');
        }

        $module = $lesson->loadMissing('module')->module;

        if ($module !== null && ! $module->isUnlockedFor($enrollment->enrolled_at)) {
            $unlocksAt = $module->unlocksAt($enrollment->enrolled_at);

            return Response::deny($unlocksAt !== null
                ? 'This lesson unlocks on '.$unlocksAt->format('j F Y').'.'
                : 'This lesson is not available yet.');
        }

        return Response::allow();
    }

    /**
     * Only an enrolled student with an unlocked module records progress;
     * previews stay stateless and locked content cannot be marked complete
     * by calling the endpoint directly.
     */
    public function trackProgress(User $user, Lesson $lesson): bool
    {
        return $this->view($user, $lesson)->allowed() && ! $lesson->isFreePreview();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTrainer();
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $lesson->loadMissing('course')->course->isOwnedBy($user);
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->isAdmin() || $lesson->loadMissing('course')->course->isOwnedBy($user);
    }
}
