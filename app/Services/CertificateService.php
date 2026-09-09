<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\CourseCompletionMode;
use App\Enums\EnrollmentStatus;
use App\Enums\LiveClassStatus;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LiveClassAttendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Issues one certificate per student per course, honouring the course's
 * configured completion rules (§11): which lessons must be finished, and
 * whether every attached live class must also have been attended.
 */
class CertificateService
{
    /**
     * Whether the student has met this course's completion requirements.
     *
     * A certificate is never issued on lesson completion alone if the course
     * also requires live-class attendance — both conditions must hold.
     */
    public function isEligible(User $user, Course $course): bool
    {
        if (! $course->certificate_enabled) {
            return false;
        }

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment === null || ! $enrollment->grantsAccess()) {
            return false;
        }

        $threshold = $course->completion_threshold ?: (int) config('academy.certificate_threshold', 100);
        $lessonsSatisfied = $enrollment->status === EnrollmentStatus::Completed
            || $enrollment->progress_percent >= $threshold;

        if (! $lessonsSatisfied) {
            return false;
        }

        return ! $course->completion_requires_live_attendance || $this->hasAttendedAllRequiredClasses($user, $course);
    }

    /**
     * Every live class attached to the course that has already taken place
     * must have a "present" attendance record for this student.
     */
    public function hasAttendedAllRequiredClasses(User $user, Course $course): bool
    {
        $pastClassIds = $course->liveClasses()
            ->whereNot('status', LiveClassStatus::Cancelled)
            ->where('scheduled_at', '<=', now())
            ->pluck('id');

        if ($pastClassIds->isEmpty()) {
            return true;
        }

        $presentCount = LiveClassAttendance::query()
            ->where('user_id', $user->id)
            ->whereIn('live_class_id', $pastClassIds)
            ->where('status', AttendanceStatus::Present)
            ->count();

        return $presentCount >= $pastClassIds->count();
    }

    /**
     * Issue the certificate when eligible, or return the existing one.
     *
     * The unique (user_id, course_id) index is the real guard against
     * duplicates; `firstOrCreate` inside a transaction keeps the common path
     * free of exceptions.
     */
    public function issueIfEligible(User $user, Course $course): ?Certificate
    {
        $existing = $this->find($user, $course);

        if ($existing !== null) {
            return $existing;
        }

        if (! $this->isEligible($user, $course)) {
            return null;
        }

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return DB::transaction(fn (): Certificate => Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'certificate_number' => $this->generateNumber(),
                'student_name' => $user->name,
                'course_title' => $course->title,
                'instructor_name' => $course->instructor?->name,
                'completed_on' => ($enrollment?->completed_at ?? now())->toDateString(),
                'issued_at' => now(),
            ],
        ));
    }

    public function find(User $user, Course $course): ?Certificate
    {
        return Certificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
    }

    /**
     * The lessons that count toward this course's completion percentage and
     * certificate eligibility, honouring `completion_mode`.
     *
     * @return Builder<Lesson>
     */
    public function countableLessonsQuery(Course $course): Builder
    {
        return Lesson::query()
            ->where('course_id', $course->id)
            ->where('is_published', true)
            ->when(
                $course->completion_mode === CourseCompletionMode::RequiredLessonsOnly,
                fn ($query) => $query->where('is_required', true),
            );
    }

    /**
     * The field values a certificate image is rendered from, keyed to match
     * `CertificateTemplate::REQUIRED_FIELDS` / `OPTIONAL_FIELDS`.
     *
     * @return array<string, string|null>
     */
    public function renderData(Certificate $certificate): array
    {
        return [
            'student_name' => $certificate->student_name,
            'course_name' => $certificate->course_title,
            'completion_date' => $certificate->completed_on->format('j F Y'),
            'certificate_id' => $certificate->certificate_number,
            'instructor_name' => $certificate->instructor_name,
            'academy_name' => app(SettingsRepository::class)->get('academy_name', config('academy.name')),
            'verification_url' => route('certificates.verify', $certificate),
        ];
    }

    /**
     * Human-readable, non-sequential identifier: OA-2026-K3F9QA.
     */
    private function generateNumber(): string
    {
        do {
            $number = sprintf('OA-%s-%s', now()->format('Y'), Str::upper(Str::random(6)));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }
}
