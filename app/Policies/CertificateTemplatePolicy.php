<?php

namespace App\Policies;

use App\Models\CertificateTemplate;
use App\Models\User;

class CertificateTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->isAdmin();
    }
}
