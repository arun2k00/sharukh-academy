<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\CourseModuleRequest;
use App\Models\Course;
use App\Models\CourseModule;
use App\Services\CurriculumOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseModuleController extends Controller
{
    public function store(CourseModuleRequest $request, Course $course, CurriculumOrderService $ordering): RedirectResponse
    {
        Gate::authorize('update', $course);

        $course->modules()->create([
            ...$request->safe()->only(['title', 'description', 'unlock_type', 'unlock_after_days', 'unlock_at']),
            'sort_order' => $request->integer('sort_order') ?: $ordering->nextPositionForModule($course->id),
        ]);

        return redirect()->route('tutor.courses.curriculum', $course)
            ->with('status', 'Module added.');
    }

    public function edit(CourseModule $module): View
    {
        $module->loadMissing('course');

        Gate::authorize('update', $module->course);

        return view('tutor.modules.edit', [
            'module' => $module,
            'course' => $module->course,
        ]);
    }

    public function update(CourseModuleRequest $request, CourseModule $module): RedirectResponse
    {
        $module->loadMissing('course');

        Gate::authorize('update', $module->course);

        $module->update($request->safe()->only([
            'title', 'description', 'sort_order', 'unlock_type', 'unlock_after_days', 'unlock_at',
        ]));

        return redirect()->route('tutor.courses.curriculum', $module->course)
            ->with('status', 'Module updated.');
    }

    public function destroy(CourseModule $module): RedirectResponse
    {
        $course = $module->loadMissing('course')->course;

        Gate::authorize('update', $course);

        $lessonCount = $module->lessons()->count();

        $module->delete();

        return redirect()->route('tutor.courses.curriculum', $course)
            ->with('status', $lessonCount > 0
                ? "Module deleted along with {$lessonCount} lesson(s)."
                : 'Module deleted.');
    }
}
