<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseQuestion;
use Illuminate\View\View;

/**
 * A staff-wide view across every course's Q&A (§14), unanswered first, so
 * nothing sits unanswered because it landed inside one specific course.
 */
class CourseQuestionController extends Controller
{
    public function index(): View
    {
        $questions = CourseQuestion::query()
            ->withCount('replies')
            ->with(['user', 'course', 'lesson'])
            ->orderByRaw('replies_count = 0 desc')
            ->latest('id')
            ->paginate(20);

        return view('admin.questions.index', ['questions' => $questions]);
    }
}
