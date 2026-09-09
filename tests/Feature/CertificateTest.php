<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CertificateService;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Complete every published lesson of a course for a student.
     */
    private function completeCourse(User $student, int $courseId): void
    {
        $progress = app(ProgressService::class);

        foreach (Lesson::where('course_id', $courseId)->get() as $lesson) {
            $progress->markComplete($student, $lesson);
        }
    }

    public function test_a_certificate_is_issued_once_the_course_is_complete(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 2);
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->completeCourse($student, $course->id);

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_no_certificate_is_issued_before_the_threshold_is_reached(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 4);
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        app(ProgressService::class)->markComplete($student, $this->firstLesson($course));

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_a_course_with_certificates_disabled_issues_nothing(): void
    {
        $course = $this->courseWithCurriculum(['certificate_enabled' => false], modules: 1, lessonsPerModule: 2);
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->completeCourse($student, $course->id);

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_a_second_certificate_is_never_issued_for_the_same_course(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 2);
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->completeCourse($student, $course->id);

        $service = app(CertificateService::class);
        $service->issueIfEligible($student, $course);
        $service->issueIfEligible($student, $course);

        $this->assertSame(1, Certificate::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->count());
    }

    public function test_certificate_numbers_are_unique(): void
    {
        $student = $this->student();
        $service = app(CertificateService::class);
        $numbers = [];

        foreach (range(1, 5) as $ignored) {
            $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
            Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);
            $this->completeCourse($student, $course->id);

            $numbers[] = $service->find($student, $course)?->certificate_number;
        }

        $this->assertCount(5, array_filter($numbers));
        $this->assertSame($numbers, array_unique($numbers));
    }

    public function test_a_student_can_only_see_their_own_certificate(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
        $owner = $this->student();
        Enrollment::factory()->create(['user_id' => $owner->id, 'course_id' => $course->id]);
        $this->completeCourse($owner, $course->id);

        $certificate = Certificate::firstOrFail();

        $this->actingAs($owner)->get(route('certificates.show', $certificate))->assertOk();
        $this->actingAs($this->student())->get(route('certificates.show', $certificate))->assertForbidden();
    }

    public function test_the_printable_view_renders_for_the_owner(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
        $owner = $this->student();
        Enrollment::factory()->create(['user_id' => $owner->id, 'course_id' => $course->id]);
        $this->completeCourse($owner, $course->id);

        $certificate = Certificate::firstOrFail();

        $this->actingAs($owner)
            ->get(route('certificates.print', $certificate))
            ->assertOk()
            ->assertSee($certificate->certificate_number);
    }

    public function test_anyone_can_verify_a_certificate_by_its_number(): void
    {
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
        $owner = $this->student();
        Enrollment::factory()->create(['user_id' => $owner->id, 'course_id' => $course->id]);
        $this->completeCourse($owner, $course->id);

        $certificate = Certificate::firstOrFail();

        $this->get(route('certificates.verify', $certificate))
            ->assertOk()
            ->assertSee($certificate->student_name)
            ->assertSee($certificate->course_title);
    }

    public function test_verifying_an_unknown_number_returns_not_found(): void
    {
        $this->get(route('certificates.verify', 'OA-2026-NOPE12'))->assertNotFound();
    }
}
