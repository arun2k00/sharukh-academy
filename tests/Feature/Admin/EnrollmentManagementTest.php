<?php

namespace Tests\Feature\Admin;

use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_enrols_a_student_manually(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        $this->actingAs($this->admin())
            ->post(route('admin.enrollments.store'), [
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.enrollments.index'));

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active->value,
            'source' => EnrollmentSource::Admin->value,
        ]);

        // The manual enrolment really does open the course.
        $this->actingAs($student)
            ->get(route('learn.course', $course))
            ->assertRedirect(route('learn.lesson', [$course, $this->firstLesson($course)]));
    }

    public function test_a_duplicate_enrolment_is_reported_rather_than_failing_at_the_database(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->post(route('admin.enrollments.store'), [
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ])
            ->assertSessionHasErrors('user_id');

        $this->assertDatabaseCount('enrollments', 1);
    }

    public function test_changing_the_status_to_cancelled_revokes_access(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->patch(route('admin.enrollments.update', $enrollment), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertSame(EnrollmentStatus::Cancelled, $enrollment->fresh()->status);

        $this->actingAs($student)->get(route('learn.course', $course))->assertForbidden();
    }

    public function test_removing_an_enrolment_revokes_access(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        $enrollment = Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.enrollments.destroy', $enrollment))
            ->assertRedirect();

        $this->actingAs($student)->get(route('learn.course', $course))->assertForbidden();
    }

    public function test_an_expiry_date_must_be_in_the_future(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();

        $this->actingAs($this->admin())
            ->post(route('admin.enrollments.store'), [
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
                'expires_at' => now()->subWeek()->toDateString(),
            ])
            ->assertSessionHasErrors('expires_at');
    }

    public function test_a_student_cannot_enrol_themselves_through_the_admin_endpoint(): void
    {
        $course = $this->courseWithCurriculum(['price' => 19900]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('admin.enrollments.store'), [
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('enrollments', 0);
    }
}
