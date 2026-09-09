<?php

namespace Database\Factories;

use App\Models\LiveClass;
use App\Models\LiveClassResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LiveClassResource>
 */
class LiveClassResourceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'live_class_id' => LiveClass::factory(),
            'title' => Str::title(fake()->words(3, true)),
            'file_path' => 'live-class-resources/demo/'.Str::random(20).'.pdf',
            'original_name' => Str::slug(fake()->words(3, true)).'.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'file_size' => fake()->numberBetween(80_000, 2_000_000),
            'sort_order' => 1,
        ];
    }
}
