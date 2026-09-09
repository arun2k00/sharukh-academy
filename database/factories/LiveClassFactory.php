<?php

namespace Database\Factories;

use App\Enums\LiveClassStatus;
use App\Models\Course;
use App\Models\LiveClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LiveClass>
 */
class LiveClassFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(4, true));

        return [
            'course_id' => Course::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'preparation_notes' => fake()->sentence(20),
            'scheduled_at' => now()->addDays(fake()->numberBetween(2, 30))->setTime(18, 30),
            'duration_minutes' => 60,
            'zoom_url' => 'https://us02web.zoom.us/j/'.fake()->numerify('###########'),
            'zoom_meeting_id' => fake()->numerify('### #### ####'),
            'status' => LiveClassStatus::Scheduled,
        ];
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => now()->subDays(fake()->numberBetween(5, 60))->setTime(18, 30),
            'status' => LiveClassStatus::Completed,
            'recording_url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
        ]);
    }
}
