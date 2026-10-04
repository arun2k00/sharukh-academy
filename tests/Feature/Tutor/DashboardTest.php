<?php

namespace Tests\Feature\Tutor;

use App\Models\Course;
use App\Models\LiveClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_renders_with_an_upcoming_live_class_without_lazy_loading_its_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        LiveClass::factory()->create([
            'course_id' => $course->id,
            'instructor_id' => $trainer->instructor->id,
        ]);

        $this->actingAs($trainer)
            ->get(route('tutor.dashboard'))
            ->assertOk()
            ->assertSee($course->title);
    }
}
