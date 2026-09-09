<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway' => 'manual',
            'amount' => fake()->randomElement([4900, 8900, 12900]),
            'currency' => config('academy.currency'),
            'status' => PaymentStatus::Pending,
        ];
    }
}
