<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonBookmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Toggles a bookmark on one lesson (§16).
 */
class LessonBookmarkController extends Controller
{
    public function __invoke(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('view', $lesson);

        $user = $request->user();

        $deleted = LessonBookmark::query()
            ->where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->delete();

        if ($deleted === 0) {
            LessonBookmark::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'course_id' => $course->id,
            ]);
        }

        return back()->with('status', $deleted > 0 ? 'Bookmark removed.' : 'Lesson bookmarked.');
    }
}
