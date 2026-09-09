<?php

namespace App\Services;

use App\Models\Course;

/**
 * Keeps the denormalised course totals in step with its curriculum.
 */
class CourseMetricsService
{
    /**
     * Recalculate the advertised course length from its published lessons.
     */
    public function refresh(Course $course): Course
    {
        $minutes = (int) $course->lessons()->where('is_published', true)->sum('duration_minutes');

        $course->forceFill(['duration_minutes' => $minutes])->save();

        return $course;
    }
}
