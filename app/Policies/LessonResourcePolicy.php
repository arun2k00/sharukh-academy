<?php

namespace App\Policies;

use App\Models\LessonResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class LessonResourcePolicy
{
    /**
     * A resource inherits the access rules of the lesson it belongs to.
     */
    public function download(User $user, LessonResource $resource): bool
    {
        return Gate::forUser($user)->allows('view', $resource->loadMissing('lesson')->lesson);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LessonResource $resource): bool
    {
        return $user->isAdmin();
    }
}
