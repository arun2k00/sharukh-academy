<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'certificate_number' => 'OA-'.now()->format('Y').'-'.Str::upper(Str::random(6)),
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'student_name' => fake()->name(),
            'course_title' => Str::title(fake()->words(4, true)),
            'instructor_name' => fake()->name(),
            'completed_on' => now()->subDays(fake()->numberBetween(1, 60))->toDateString(),
            'issued_at' => now()->subDays(fake()->numberBetween(0, 60)),
        ];
    }
}
