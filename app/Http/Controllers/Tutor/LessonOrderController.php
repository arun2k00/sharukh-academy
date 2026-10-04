<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\CurriculumOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LessonOrderController extends Controller
{
    public function __invoke(Request $request, Lesson $lesson, CurriculumOrderService $ordering): RedirectResponse
    {
        Gate::authorize('update', $lesson);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $ordering->moveLesson($lesson, $validated['direction']);

        return back()->with('status', 'Lesson order updated.');
    }
}
