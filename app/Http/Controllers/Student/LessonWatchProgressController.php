<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Receives playback position updates from the YouTube player.
 *
 * The reported values only ever raise the stored watch percentage, and the
 * lesson still has to belong to a course the student is enrolled in.
 */
class LessonWatchProgressController extends Controller
{
    public function __invoke(
        Request $request,
        Course $course,
        Lesson $lesson,
        ProgressService $progress,
    ): JsonResponse {
        Gate::authorize('trackProgress', $lesson);

        $validated = $request->validate([
            'seconds' => ['required', 'integer', 'min:0', 'max:86400'],
            'percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $record = $progress->recordWatchProgress(
            $request->user(),
            $lesson,
            (int) $validated['seconds'],
            (int) $validated['percent'],
        );

        return response()->json([
            'completed' => $record->is_completed,
            'watched_percent' => $record->watched_percent,
            'course_percent' => $progress->percentFor($request->user(), $course),
        ]);
    }
}
