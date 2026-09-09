<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Issues one certificate per student per course.
 */
class CertificateService
{
    /**
     * Whether the student has met this course's completion requirements.
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

        return $enrollment->status === EnrollmentStatus::Completed
            || $enrollment->progress_percent >= $threshold;
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
                'instructor_name' => $course->loadMissing('instructor')->instructor?->name,
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
