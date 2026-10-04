<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_trainer_demo_login_signs_in_and_redirects_to_the_tutor_dashboard(): void
    {
        $trainer = $this->trainer();
        $trainer->forceFill(['email' => 'trainer@aixone.site'])->save();

        $this->post(route('demo-login', 'trainer'))
            ->assertRedirect(route('tutor.dashboard'));

        $this->assertAuthenticatedAs($trainer->fresh());
        $this->assertTrue(session('is_demo_session'));
    }

    public function test_the_student_demo_login_signs_in_and_redirects_to_the_dashboard(): void
    {
        $student = $this->student();
        $student->forceFill(['email' => 'student@aixone.site'])->save();

        $this->post(route('demo-login', 'student'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student->fresh());
        $this->assertTrue(session('is_demo_session'));
    }

    public function test_the_admin_demo_login_no_longer_exists(): void
    {
        $this->post(route('demo-login', 'admin'))->assertNotFound();
    }

    public function test_a_demo_trainer_can_browse_but_cannot_create_a_course(): void
    {
        $trainer = $this->trainer();
        $trainer->forceFill(['email' => 'trainer@aixone.site'])->save();

        $this->post(route('demo-login', 'trainer'));

        $this->get(route('tutor.dashboard'))->assertOk();

        $this->post(route('tutor.courses.store'), [
            'title' => 'Smuggled course',
            'short_description' => 'x',
            'price' => 0,
            'level' => 'beginner',
            'language' => 'English',
            'status' => 'draft',
            'sort_order' => 0,
            'completion_threshold' => 100,
            'access_type' => 'lifetime',
            'completion_mode' => 'all_lessons',
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_a_demo_trainer_can_still_log_out(): void
    {
        $trainer = $this->trainer();
        $trainer->forceFill(['email' => 'trainer@aixone.site'])->save();

        $this->post(route('demo-login', 'trainer'));

        $this->post(route('logout'))->assertRedirect();

        $this->assertGuest();
    }

    public function test_a_demo_student_cannot_enroll_in_a_course(): void
    {
        $student = $this->student();
        $student->forceFill(['email' => 'student@aixone.site'])->save();
        $course = $this->courseWithCurriculum(['price' => 0]);

        $this->post(route('demo-login', 'student'));

        $this->post(route('courses.enroll', $course))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_signing_in_normally_as_the_same_account_is_not_restricted(): void
    {
        $trainer = $this->trainer();
        $trainer->forceFill(['email' => 'trainer@aixone.site'])->save();

        $this->actingAs($trainer)
            ->post(route('tutor.courses.store'), [
                'title' => 'A real course',
                'short_description' => 'x',
                'price' => 0,
                'level' => 'beginner',
                'language' => 'English',
                'status' => 'draft',
                'sort_order' => 0,
                'completion_threshold' => 100,
                'access_type' => 'lifetime',
                'completion_mode' => 'all_lessons',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('courses', 1);
    }
}
