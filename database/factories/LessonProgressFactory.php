<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonProgress>
 */
class LessonProgressFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'course_id' => Course::factory(),
            'is_completed' => true,
            'watched_seconds' => fake()->numberBetween(120, 2400),
            'watched_percent' => 100,
            'completed_at' => now()->subDays(fake()->numberBetween(0, 30)),
            'last_viewed_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }

    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
            'watched_percent' => fake()->numberBetween(5, 80),
            'completed_at' => null,
        ]);
    }
}
