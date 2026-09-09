<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseQuestionReplyController extends Controller
{
    public function __invoke(Request $request, CourseQuestion $question): RedirectResponse
    {
        Gate::authorize('learn', $question->loadMissing('course')->course);

        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ]);

        $question->replies()->create([
            'user_id' => $request->user()->id,
            'reply' => $validated['reply'],
        ]);

        return back()->with('status', 'Reply posted.');
    }
}
