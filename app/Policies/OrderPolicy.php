<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin sees every order; a student may see the receipt for their own.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }

    public function refund(User $user, Order $order): bool
    {
        return $user->isAdmin() && $order->isPaid();
    }
}
