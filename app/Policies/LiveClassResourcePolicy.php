<?php

namespace App\Policies;

use App\Models\LiveClassResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class LiveClassResourcePolicy
{
    public function download(User $user, LiveClassResource $resource): bool
    {
        return Gate::forUser($user)->allows('view', $resource->loadMissing('liveClass')->liveClass);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LiveClassResource $resource): bool
    {
        return $user->isAdmin();
    }
}
