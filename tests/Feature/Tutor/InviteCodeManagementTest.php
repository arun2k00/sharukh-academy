<?php

namespace Tests\Feature\Tutor;

use App\Models\Course;
use App\Models\InviteCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteCodeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_trainer_generates_a_code_for_their_own_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);

        $this->actingAs($trainer)
            ->post(route('tutor.courses.invite-codes.store', $course), ['max_uses' => 5])
            ->assertRedirect(route('tutor.courses.invite-codes.index', $course));

        $code = InviteCode::firstOrFail();

        $this->assertSame($course->id, $code->course_id);
        $this->assertSame($trainer->id, $code->created_by);
        $this->assertSame(5, $code->max_uses);
    }

    public function test_a_trainer_cannot_generate_a_code_for_a_course_they_do_not_own(): void
    {
        $trainer = $this->trainer();
        $someoneElsesCourse = Course::factory()->create();

        $this->actingAs($trainer)
            ->post(route('tutor.courses.invite-codes.store', $someoneElsesCourse), [])
            ->assertForbidden();

        $this->assertDatabaseCount('invite_codes', 0);
    }

    public function test_a_trainer_revokes_a_code_on_their_own_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $code = InviteCode::factory()->create(['course_id' => $course->id, 'created_by' => $trainer->id]);

        $this->actingAs($trainer)
            ->delete(route('tutor.invite-codes.destroy', $code))
            ->assertRedirect(route('tutor.courses.invite-codes.index', $course));

        $this->assertNotNull($code->fresh()->revoked_at);
    }

    public function test_the_index_lists_only_that_courses_codes(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $otherCourse = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);

        InviteCode::factory()->create(['course_id' => $course->id, 'code' => 'MINE12345A']);
        InviteCode::factory()->create(['course_id' => $otherCourse->id, 'code' => 'NOTMINE123']);

        $this->actingAs($trainer)
            ->get(route('tutor.courses.invite-codes.index', $course))
            ->assertOk()
            ->assertSee('MINE12345A')
            ->assertDontSee('NOTMINE123');
    }
}
