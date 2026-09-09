<?php

namespace Database\Factories;

use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'status' => EnrollmentStatus::Active,
            'source' => EnrollmentSource::Admin,
            'progress_percent' => 0,
            'enrolled_at' => now()->subDays(fake()->numberBetween(1, 90)),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnrollmentStatus::Completed,
            'progress_percent' => 100,
            'completed_at' => now()->subDays(fake()->numberBetween(1, 20)),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => EnrollmentStatus::Cancelled]);
    }
}
