<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Course Q&A (§14): kept deliberately structured — questions and replies,
 * scoped to a course the student is enrolled in, nothing more social than that.
 */
class CourseQuestionController extends Controller
{
    public function index(Course $course): View
    {
        Gate::authorize('learn', $course);

        return view('student.questions.index', [
            'course' => $course,
            'questions' => $course->questions()
                ->with(['user', 'lesson', 'replies.user'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('learn', $course);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
            'lesson_id' => ['nullable', 'integer', 'exists:lessons,id'],
        ]);

        $course->questions()->create([
            'user_id' => $request->user()->id,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'question' => $validated['question'],
        ]);

        return back()->with('status', 'Your question has been posted.');
    }

    public function destroy(Request $request, CourseQuestion $question): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $question->user_id === $user->id, 403);

        $question->delete();

        return back()->with('status', 'Question removed.');
    }
}
