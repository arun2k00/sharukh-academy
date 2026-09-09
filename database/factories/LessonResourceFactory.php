<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LessonResource>
 */
class LessonResourceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::slug(fake()->words(3, true)).'.pdf';

        return [
            'lesson_id' => Lesson::factory(),
            'title' => Str::title(fake()->words(3, true)),
            'file_path' => 'lesson-resources/demo/'.Str::random(20).'.pdf',
            'original_name' => $name,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'file_size' => fake()->numberBetween(80_000, 4_000_000),
            'sort_order' => 1,
        ];
    }
}
