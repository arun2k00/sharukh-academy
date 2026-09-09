<?php

namespace Database\Factories;

use App\Models\CourseQuestion;
use App\Models\CourseQuestionReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseQuestionReply>
 */
class CourseQuestionReplyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_question_id' => CourseQuestion::factory(),
            'user_id' => User::factory(),
            'reply' => fake()->paragraph(),
            'is_pinned' => false,
        ];
    }
}
