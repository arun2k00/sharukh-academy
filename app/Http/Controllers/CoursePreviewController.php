<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Plays a lesson that has been marked as a free preview.
 *
 * Authorisation runs through the same lesson policy as the paid learning area,
 * so a lesson that is not a preview is never playable from here.
 */
class CoursePreviewController extends Controller
{
    public function __invoke(Course $course, Lesson $lesson): View
    {
        Gate::authorize('view', $course);
        abort_unless($lesson->isFreePreview(), 404);
        Gate::authorize('view', $lesson);

        $lesson->load('resources');

        return view('public.courses.preview', [
            'course' => $course,
            'lesson' => $lesson,
        ]);
    }
}
