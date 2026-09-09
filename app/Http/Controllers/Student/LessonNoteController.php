<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Private per-lesson notes (§15), optionally pinned to a video moment.
 */
class LessonNoteController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('view', $lesson);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
            'video_timestamp_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
        ]);

        LessonNote::create([
            'user_id' => $request->user()->id,
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            ...$validated,
        ]);

        return back()->with('status', 'Note saved.');
    }

    public function update(Request $request, LessonNote $note): RedirectResponse
    {
        $this->authorizeOwnership($request, $note);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:4000'],
            'video_timestamp_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
        ]);

        $note->update($validated);

        return back()->with('status', 'Note updated.');
    }

    public function destroy(Request $request, LessonNote $note): RedirectResponse
    {
        $this->authorizeOwnership($request, $note);

        $note->delete();

        return back()->with('status', 'Note deleted.');
    }

    /**
     * Notes are private: only their author may ever read, edit or delete
     * them — not even an administrator has a route to another student's notes.
     */
    private function authorizeOwnership(Request $request, LessonNote $note): void
    {
        abort_unless($note->user_id === $request->user()->id, 403);
    }
}
