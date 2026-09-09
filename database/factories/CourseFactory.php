<?php

namespace Database\Factories;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(4, true));

        return [
            'category_id' => Category::factory(),
            'instructor_id' => Instructor::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->sentence(14),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomElement([0, 4900, 8900, 12900]),
            'level' => fake()->randomElement(CourseLevel::cases()),
            'language' => 'English',
            'status' => CourseStatus::Published,
            'is_featured' => false,
            'sort_order' => 0,
            'outcomes' => fake()->sentences(4),
            'requirements' => fake()->sentences(2),
            'audience' => fake()->sentences(3),
            'certificate_enabled' => true,
            'completion_threshold' => 100,
            'published_at' => now()->subDays(fake()->numberBetween(1, 200)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => ['price' => 0]);
    }
}
