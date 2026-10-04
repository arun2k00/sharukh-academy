<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\InviteCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteCodeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_generates_a_code_for_any_course(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.courses.invite-codes.store', $course), ['max_uses' => 10])
            ->assertRedirect(route('admin.courses.invite-codes.index', $course));

        $code = InviteCode::firstOrFail();

        $this->assertSame($course->id, $code->course_id);
        $this->assertSame($admin->id, $code->created_by);
    }

    public function test_an_admin_revokes_any_courses_code(): void
    {
        $admin = $this->admin();
        $course = Course::factory()->create();
        $code = InviteCode::factory()->create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->delete(route('admin.invite-codes.destroy', $code))
            ->assertRedirect(route('admin.courses.invite-codes.index', $course));

        $this->assertNotNull($code->fresh()->revoked_at);
    }

    public function test_a_student_cannot_manage_invite_codes(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->student())
            ->get(route('admin.courses.invite-codes.index', $course))
            ->assertForbidden();

        $this->actingAs($this->student())
            ->post(route('admin.courses.invite-codes.store', $course), [])
            ->assertForbidden();
    }
}
