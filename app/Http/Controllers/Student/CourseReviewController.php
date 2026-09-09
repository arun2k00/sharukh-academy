<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * A student may review a course they are enrolled in (§22). Submitting a
 * second review updates the first — the unique index on (course_id, user_id)
 * makes that the only possible outcome rather than a database error.
 */
class CourseReviewController extends Controller
{
    public function __invoke(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('learn', $course);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        CourseReview::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $request->user()->id],
            $validated,
        );

        return back()->with('status', 'Thanks — your review has been saved.');
    }
}
