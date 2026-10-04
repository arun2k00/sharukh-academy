<?php

namespace Tests\Feature;

use App\Enums\EnrollmentSource;
use App\Models\Course;
use App\Models\InviteCode;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteCodeRedemptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_redeems_a_valid_code_and_gains_access(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->create(['course_id' => $course->id]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertRedirect(route('learn.course', $course));

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'source' => EnrollmentSource::Invite->value,
            'invite_code_id' => $code->id,
        ]);

        $this->assertSame(1, $code->fresh()->used_count);
    }

    public function test_redemption_is_case_insensitive_and_trims_whitespace(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->create(['course_id' => $course->id, 'code' => 'ABCD123456']);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll.invite', $course), ['code' => ' abcd123456 '])
            ->assertRedirect(route('learn.course', $course));

        $this->assertSame(1, $code->fresh()->used_count);
    }

    public function test_an_unknown_code_shows_a_validation_error_and_grants_nothing(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll.invite', $course), ['code' => 'DOESNOTEXIST'])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_code_generated_for_a_different_course_is_rejected(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $otherCourse = Course::factory()->create();
        $code = InviteCode::factory()->create(['course_id' => $otherCourse->id]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('enrollments', 0);
        $this->assertSame(0, $code->fresh()->used_count);
    }

    public function test_an_expired_code_is_rejected(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->expired()->create(['course_id' => $course->id]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_revoked_code_is_rejected(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->revoked()->create(['course_id' => $course->id]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_an_exhausted_code_is_rejected(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->exhausted()->create(['course_id' => $course->id]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_student_already_enrolled_cannot_redeem_a_code(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->create(['course_id' => $course->id]);
        $student = $this->student();

        app(EnrollmentService::class)->enroll($student, $course, EnrollmentSource::Free);

        $this->actingAs($student)
            ->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertForbidden();

        $this->assertSame(0, $code->fresh()->used_count);
    }

    public function test_a_guest_cannot_redeem_a_code(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);
        $code = InviteCode::factory()->create(['course_id' => $course->id]);

        $this->post(route('courses.enroll.invite', $course), ['code' => $code->code])
            ->assertRedirect(route('login'));

        $this->assertSame(0, $code->fresh()->used_count);
    }
}
