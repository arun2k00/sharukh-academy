<?php

namespace Database\Factories;

use App\Models\CareerPath;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CareerPath>
 */
class CareerPathFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'title' => Str::title($title),
            'subtitle' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
