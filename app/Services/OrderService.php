<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Creates the order record that a payment gateway later settles.
 */
class OrderService
{
    /**
     * Open a pending order, reusing one the student already has for the course.
     */
    public function openFor(User $user, Course $course): Order
    {
        return DB::transaction(function () use ($user, $course): Order {
            // Locked: without it a double-click opens two pending orders for
            // the same course, and the second one never gets settled.
            $pending = Order::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', OrderStatus::Pending)
                ->lockForUpdate()
                ->first();

            if ($pending !== null) {
                return $pending;
            }

            return Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->price,
                'currency' => config('academy.currency'),
                'status' => OrderStatus::Pending,
            ]);
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'OA'.now()->format('ymd').Str::upper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
