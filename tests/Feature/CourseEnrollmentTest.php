<?php

namespace Tests\Feature;

use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_can_enrol_themselves_in_a_free_course(): void
    {
        $course = $this->courseWithCurriculum(['price' => 0]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll', $course))
            ->assertRedirect(route('learn.course', $course));

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => EnrollmentStatus::Active->value,
            'source' => EnrollmentSource::Free->value,
        ]);
    }

    public function test_a_paid_course_records_a_pending_order_and_grants_no_access(): void
    {
        $course = $this->courseWithCurriculum(['price' => 12900]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll', $course))
            ->assertRedirect(route('courses.show', $course))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('orders', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => OrderStatus::Pending->value,
        ]);

        // Crucially, no enrolment was created by the unpaid order.
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $this->actingAs($student)
            ->get(route('learn.course', $course))
            ->assertForbidden();
    }

    public function test_requesting_a_paid_course_twice_reuses_the_pending_order(): void
    {
        $course = $this->courseWithCurriculum(['price' => 8900]);
        $student = $this->student();

        $this->actingAs($student)->post(route('courses.enroll', $course));
        $this->actingAs($student)->post(route('courses.enroll', $course));

        $this->assertDatabaseCount('orders', 1);
    }

    public function test_a_guest_cannot_enrol(): void
    {
        $course = $this->publishedCourse();

        $this->post(route('courses.enroll', $course))->assertRedirect(route('login'));
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_draft_course_cannot_be_enrolled_in(): void
    {
        $course = Course::factory()->draft()->create(['price' => 0]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll', $course))
            ->assertForbidden();
    }

    public function test_the_service_never_creates_a_second_enrolment_for_the_same_course(): void
    {
        $course = $this->publishedCourse();
        $student = $this->student();
        $service = app(EnrollmentService::class);

        $service->enroll($student, $course);
        $service->enroll($student, $course);

        $this->assertSame(1, Enrollment::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->count());
    }

    public function test_re_enrolling_reactivates_a_cancelled_enrolment(): void
    {
        $course = $this->publishedCourse();
        $student = $this->student();

        Enrollment::factory()->cancelled()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $enrollment = app(EnrollmentService::class)->enroll($student, $course);

        $this->assertSame(EnrollmentStatus::Active, $enrollment->status);
        $this->assertDatabaseCount('enrollments', 1);
    }
}
