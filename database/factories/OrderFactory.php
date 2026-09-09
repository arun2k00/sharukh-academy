<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'OA'.now()->format('ymd').Str::upper(Str::random(6)),
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'amount' => fake()->randomElement([4900, 8900, 12900]),
            'currency' => config('academy.currency'),
            'status' => OrderStatus::Pending,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Paid,
            'paid_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ]);
    }
}
