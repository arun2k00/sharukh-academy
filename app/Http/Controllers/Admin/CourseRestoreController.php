<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Brings a deleted course back.
 *
 * Deletion is a soft delete, so the curriculum, enrolments and progress that
 * belonged to the course are still intact and come back with it.
 */
class CourseRestoreController extends Controller
{
    public function __invoke(Course $course): RedirectResponse
    {
        Gate::authorize('restore', $course);

        $course->restore();

        return redirect()->route('admin.courses.edit', $course)
            ->with('status', 'Course restored.');
    }
}
