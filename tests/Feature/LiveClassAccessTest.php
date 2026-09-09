<?php

namespace Tests\Feature;

use App\Enums\LiveClassStatus;
use App\Models\Enrollment;
use App\Models\LiveClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveClassAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_enrolled_students_see_a_live_class(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->student())
            ->get(route('live-classes.show', $liveClass))
            ->assertForbidden();

        $enrolled = $this->student();
        Enrollment::factory()->create(['user_id' => $enrolled->id, 'course_id' => $course->id]);

        $this->actingAs($enrolled)
            ->get(route('live-classes.show', $liveClass))
            ->assertOk()
            ->assertSee($liveClass->title);
    }

    public function test_the_zoom_link_is_never_rendered_into_the_page(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'zoom_url' => 'https://us02web.zoom.us/j/98765432100',
        ]);

        $this->actingAs($student)
            ->get(route('live-classes.show', $liveClass))
            ->assertOk()
            ->assertDontSee('us02web.zoom.us', escape: false);
    }

    public function test_joining_before_the_window_opens_is_refused(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'scheduled_at' => now()->addDays(3),
        ]);

        $this->actingAs($student)
            ->get(route('live-classes.join', $liveClass))
            ->assertForbidden();
    }

    public function test_joining_inside_the_window_redirects_to_zoom(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'scheduled_at' => now()->addMinutes(5),
            'zoom_url' => 'https://us02web.zoom.us/j/11122233344',
        ]);

        $this->actingAs($student)
            ->get(route('live-classes.join', $liveClass))
            ->assertRedirect('https://us02web.zoom.us/j/11122233344');
    }

    public function test_a_cancelled_class_cannot_be_joined(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'scheduled_at' => now()->addMinutes(5),
            'status' => LiveClassStatus::Cancelled,
        ]);

        $this->actingAs($student)
            ->get(route('live-classes.join', $liveClass))
            ->assertForbidden();
    }

    public function test_a_student_only_sees_classes_for_courses_they_are_enrolled_in(): void
    {
        $enrolledCourse = $this->courseWithCurriculum();
        $otherCourse = $this->courseWithCurriculum();

        $mine = LiveClass::factory()->create(['course_id' => $enrolledCourse->id]);
        $theirs = LiveClass::factory()->create(['course_id' => $otherCourse->id]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $enrolledCourse->id]);

        $this->actingAs($student)
            ->get(route('live-classes.index'))
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSee($theirs->title);
    }
}
