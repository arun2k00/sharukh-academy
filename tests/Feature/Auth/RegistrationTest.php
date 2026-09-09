<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_registration_screen_renders(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create your account');
    }

    public function test_a_visitor_can_register_and_is_signed_in(): void
    {
        Event::fake([Registered::class]);

        $response = $this->post(route('register'), [
            'name' => 'Asha Patel',
            'email' => 'asha@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'asha@example.test')->firstOrFail();

        $this->assertSame('Asha Patel', $user->name);
        Event::assertDispatched(Registered::class);
    }

    public function test_registration_always_creates_a_student_even_if_a_role_is_submitted(): void
    {
        $this->post(route('register'), [
            'name' => 'Privilege Escalator',
            'email' => 'sneaky@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'terms' => '1',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'sneaky@example.test')->firstOrFail();

        $this->assertSame(UserRole::Student, $user->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_registration_requires_a_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        $this->post(route('register'), [
            'name' => 'Someone',
            'email' => 'taken@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'terms' => '1',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_requires_the_terms_to_be_accepted(): void
    {
        $this->post(route('register'), [
            'name' => 'Someone',
            'email' => 'someone@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
        ])->assertSessionHasErrors('terms');

        $this->assertGuest();
    }

    public function test_registration_requires_a_matching_password_confirmation(): void
    {
        $this->post(route('register'), [
            'name' => 'Someone',
            'email' => 'someone@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'something-else',
            'terms' => '1',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
