<?php

namespace App\Policies;

use App\Models\CareerPath;
use App\Models\User;

class CareerPathPolicy
{
    /**
     * Anyone may browse the list of career paths.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * The public roadmap page is visible while published; staff can preview drafts.
     */
    public function view(?User $user, CareerPath $careerPath): bool
    {
        return $careerPath->is_published || $user?->isAdmin() === true;
    }
}
