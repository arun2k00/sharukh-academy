<?php

namespace Tests\Feature\Tutor;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TutorAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function tutorRoutes(): array
    {
        return [
            'dashboard' => ['tutor.dashboard'],
            'courses' => ['tutor.courses.index'],
            'new course' => ['tutor.courses.create'],
            'live classes' => ['tutor.live-classes.index'],
            'new live class' => ['tutor.live-classes.create'],
        ];
    }

    #[DataProvider('tutorRoutes')]
    public function test_a_guest_is_redirected_to_the_login_screen(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    #[DataProvider('tutorRoutes')]
    public function test_a_student_is_refused(string $routeName): void
    {
        $this->actingAs($this->student())
            ->get(route($routeName))
            ->assertForbidden();
    }

    #[DataProvider('tutorRoutes')]
    public function test_an_administrator_is_refused(string $routeName): void
    {
        // Admin keeps using /admin — the Tutor area is not a second door in.
        $this->actingAs($this->admin())
            ->get(route($routeName))
            ->assertForbidden();
    }

    #[DataProvider('tutorRoutes')]
    public function test_a_trainer_with_a_linked_instructor_is_allowed(string $routeName): void
    {
        $this->actingAs($this->trainer())
            ->get(route($routeName))
            ->assertOk();
    }

    #[DataProvider('tutorRoutes')]
    public function test_a_trainer_with_no_linked_instructor_is_redirected_to_the_pending_notice(string $routeName): void
    {
        $trainer = User::factory()->trainer()->create();

        $this->actingAs($trainer)
            ->get(route($routeName))
            ->assertRedirect(route('tutor.pending'));
    }

    public function test_the_pending_notice_itself_is_reachable_with_no_linked_instructor(): void
    {
        $trainer = User::factory()->trainer()->create();

        $this->actingAs($trainer)
            ->get(route('tutor.pending'))
            ->assertOk();
    }

    public function test_signing_in_sends_a_trainer_to_the_tutor_dashboard(): void
    {
        $trainer = $this->trainer();

        $response = $this->post(route('login'), [
            'email' => $trainer->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('tutor.dashboard'));
    }
}
