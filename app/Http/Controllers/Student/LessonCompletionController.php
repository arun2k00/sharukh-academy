<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\CurriculumService;
use App\Services\ProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LessonCompletionController extends Controller
{
    public function __construct(
        private readonly ProgressService $progress,
        private readonly CurriculumService $curriculum,
    ) {}

    public function store(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('trackProgress', $lesson);

        $user = $request->user();
        $this->progress->markComplete($user, $lesson);

        $next = $this->curriculum->nextLesson($course, $lesson);

        if ($next !== null) {
            return redirect()->route('learn.lesson', [$course, $next]);
        }

        return redirect()->route('learn.lesson', [$course, $lesson])
            ->with('status', 'That was the last lesson — the course is complete.');
    }

    public function destroy(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('trackProgress', $lesson);

        $this->progress->markIncomplete($request->user(), $lesson);

        return back()->with('status', 'Lesson marked as not complete.');
    }
}
