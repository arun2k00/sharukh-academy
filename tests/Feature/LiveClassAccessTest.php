<?php

namespace Tests\Feature;

use App\Enums\LiveClassStatus;
use App\Models\Enrollment;
use App\Models\LiveClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The live-class detail page (§3–§5) is intentionally public — it doubles as
 * a shareable preview — but must degrade gracefully: a guest or a
 * non-enrolled visitor is never handed the Zoom URL or the join action.
 */
class LiveClassAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_sees_a_restricted_preview_with_a_login_prompt(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $this->get(route('live-classes.show', $liveClass))
            ->assertOk()
            ->assertSee($liveClass->title)
            ->assertSee('Login to join')
            ->assertDontSee('Join live class');
    }

    public function test_an_authenticated_non_enrolled_student_is_told_to_enrol(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->student())
            ->get(route('live-classes.show', $liveClass))
            ->assertOk()
            ->assertSee('available to students enrolled')
            ->assertSee('View course')
            ->assertDontSee('Join live class');
    }

    public function test_an_enrolled_student_sees_the_full_experience(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $enrolled = $this->student();
        Enrollment::factory()->create(['user_id' => $enrolled->id, 'course_id' => $course->id]);

        $this->actingAs($enrolled)
            ->get(route('live-classes.show', $liveClass))
            ->assertOk()
            ->assertSee($liveClass->title)
            ->assertSee('Add to calendar');
    }

    public function test_the_zoom_link_is_never_rendered_into_the_page_in_any_state(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'zoom_url' => 'https://us02web.zoom.us/j/98765432100',
        ]);

        $this->get(route('live-classes.show', $liveClass))
            ->assertOk()
            ->assertDontSee('us02web.zoom.us', escape: false);

        $this->actingAs($this->student())
            ->get(route('live-classes.show', $liveClass))
            ->assertDontSee('us02web.zoom.us', escape: false);

        $enrolled = $this->student();
        Enrollment::factory()->create(['user_id' => $enrolled->id, 'course_id' => $course->id]);

        $this->actingAs($enrolled)
            ->get(route('live-classes.show', $liveClass))
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

    public function test_a_guest_cannot_reach_the_join_endpoint_at_all(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'scheduled_at' => now()->addMinutes(5),
        ]);

        $this->get(route('live-classes.join', $liveClass))->assertRedirect(route('login'));
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

    public function test_a_student_only_sees_classes_for_courses_they_are_enrolled_in_on_their_index(): void
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

    public function test_the_calendar_file_requires_authentication_and_enrolment(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $this->get(route('live-classes.calendar', $liveClass))->assertRedirect(route('login'));

        $this->actingAs($this->student())
            ->get(route('live-classes.calendar', $liveClass))
            ->assertForbidden();
    }

    public function test_the_calendar_file_never_contains_the_zoom_url(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $liveClass = LiveClass::factory()->create([
            'course_id' => $course->id,
            'zoom_url' => 'https://us02web.zoom.us/j/55566677788',
        ]);

        $response = $this->actingAs($student)->get(route('live-classes.calendar', $liveClass));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->assertStringNotContainsString('us02web.zoom.us', $response->getContent());
    }

    public function test_logging_in_via_the_join_link_redirects_back_to_the_live_class(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);
        $student = $this->student();
        $student->forceFill(['password' => 'correct-horse-battery'])->save();

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'correct-horse-battery',
            'redirect' => route('live-classes.show', $liveClass, absolute: false),
        ])->assertRedirect(route('live-classes.show', $liveClass, absolute: false));
    }

    public function test_an_external_redirect_value_is_ignored_at_login(): void
    {
        $student = $this->student();
        $student->forceFill(['password' => 'correct-horse-battery'])->save();

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'correct-horse-battery',
            'redirect' => 'https://evil.example.com/phish',
        ])->assertRedirect(route('dashboard'));
    }
}
