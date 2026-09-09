<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin() || $certificate->user_id === $user->id;
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }
}
