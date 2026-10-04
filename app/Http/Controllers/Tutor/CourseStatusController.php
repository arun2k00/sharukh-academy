<?php

namespace App\Http\Controllers\Tutor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

/**
 * Publish, unpublish or archive a course from the listing — a Trainer
 * self-publishes exactly the way Admin does, no separate approval step.
 */
class CourseStatusController extends Controller
{
    public function __invoke(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('update', $course);

        $validated = $request->validate([
            'status' => ['required', new Enum(CourseStatus::class)],
        ]);

        $status = CourseStatus::from($validated['status']);

        $course->status = $status;

        if ($status === CourseStatus::Published && $course->published_at === null) {
            $course->published_at = now();
        }

        $course->save();

        return back()->with('status', 'Course is now '.mb_strtolower($status->label()).'.');
    }
}
