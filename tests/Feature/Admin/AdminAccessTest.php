<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['admin.dashboard'],
            'courses' => ['admin.courses.index'],
            'new course' => ['admin.courses.create'],
            'live classes' => ['admin.live-classes.index'],
            'students' => ['admin.students.index'],
            'enrolments' => ['admin.enrollments.index'],
            'certificates' => ['admin.certificates.index'],
            'categories' => ['admin.categories.index'],
            'instructors' => ['admin.instructors.index'],
            'testimonials' => ['admin.testimonials.index'],
            'faqs' => ['admin.faqs.index'],
            'messages' => ['admin.messages.index'],
            'settings' => ['admin.settings.edit'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_a_guest_is_redirected_to_the_login_screen(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    #[DataProvider('adminRoutes')]
    public function test_a_student_is_refused(string $routeName): void
    {
        $this->actingAs($this->student())
            ->get(route($routeName))
            ->assertForbidden();
    }

    #[DataProvider('adminRoutes')]
    public function test_an_administrator_is_allowed(string $routeName): void
    {
        $this->actingAs($this->admin())
            ->get(route($routeName))
            ->assertOk();
    }

    public function test_a_student_cannot_create_a_course_by_posting_directly(): void
    {
        $this->actingAs($this->student())
            ->post(route('admin.courses.store'), [
                'title' => 'Unauthorised course',
                'short_description' => 'Should never be created.',
                'price' => 0,
                'level' => 'beginner',
                'language' => 'English',
                'status' => 'published',
                'sort_order' => 0,
                'completion_threshold' => 100,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('courses', 0);
    }

    public function test_a_student_cannot_delete_a_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->student())
            ->delete(route('admin.courses.destroy', $course))
            ->assertForbidden();

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'deleted_at' => null]);
    }
}
