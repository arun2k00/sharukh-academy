<?php

namespace Database\Factories;

use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LiveClassAttendance>
 */
class LiveClassAttendanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'live_class_id' => LiveClass::factory(),
            'user_id' => User::factory(),
            'status' => 'present',
            'marked_at' => now(),
        ];
    }
}
