<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoLiveHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_baseline_security_headers_are_sent(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $this->assertNull($response->headers->get('X-Powered-By'));
    }

    public function test_registration_is_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', $this->registration($i));
        }

        $this->post('/register', $this->registration(99))->assertStatus(429);
    }

    public function test_sitemap_lists_published_courses_and_robots_points_at_it(): void
    {
        $published = Course::factory()->create();
        $draft = Course::factory()->draft()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('courses.show', $published), escape: false)
            ->assertDontSee(route('courses.show', $draft), escape: false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee(route('sitemap'), escape: false);
    }

    /**
     * The regression: the public verification image 403'd for any signed-in
     * visitor who did not own the certificate, so an employer with an account
     * saw a broken image on a page that is meant to be open to everyone.
     */
    public function test_public_certificate_image_is_not_owner_gated(): void
    {
        $certificate = Certificate::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->get(route('certificates.verify-image', $certificate));

        $this->assertNotSame(403, $response->getStatusCode());
    }

    public function test_private_certificate_image_still_refuses_another_student(): void
    {
        $certificate = Certificate::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('certificates.image', $certificate))
            ->assertForbidden();
    }

    /**
     * @return array<string, string>
     */
    private function registration(int $n): array
    {
        return [
            'name' => "Signup {$n}",
            'email' => "signup{$n}@example.test",
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'terms' => '1',
        ];
    }
}
