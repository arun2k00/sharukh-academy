<?php

namespace Database\Factories;

use App\Enums\LessonType;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(4, true));

        return [
            'course_id' => Course::factory(),
            'course_module_id' => CourseModule::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'type' => LessonType::Video,
            'description' => fake()->sentence(16),
            'video_url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
            'video_id' => 'M7lc1UVf-VE',
            'duration_minutes' => fake()->numberBetween(5, 40),
            'sort_order' => 1,
            'is_preview' => false,
            'is_published' => true,
        ];
    }

    public function preview(): static
    {
        return $this->state(fn (array $attributes) => ['is_preview' => true]);
    }

    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LessonType::Text,
            'video_url' => null,
            'video_id' => null,
            'content' => fake()->paragraphs(4, true),
        ]);
    }
}
