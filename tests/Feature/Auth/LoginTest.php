<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_screen_renders(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Sign in');
    }

    public function test_a_student_signs_in_and_lands_on_the_dashboard(): void
    {
        $student = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'correct-horse-battery',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student);
    }

    public function test_an_administrator_signs_in_and_lands_on_the_admin_panel(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-horse-battery']);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'correct-horse-battery',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_signing_in_with_the_wrong_password_fails(): void
    {
        $student = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        $student = User::factory()->create(['password' => 'correct-horse-battery']);

        // The named limiter allows five attempts a minute for one address.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login'), [
                'email' => $student->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'correct-horse-battery',
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_a_signed_in_user_can_sign_out(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
