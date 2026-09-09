<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseModuleRequest;
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
            ...$request->safe()->only(['title', 'description']),
            'sort_order' => $request->integer('sort_order') ?: $ordering->nextPositionForModule($course->id),
        ]);

        return redirect()->route('admin.courses.curriculum', $course)
            ->with('status', 'Module added.');
    }

    public function edit(CourseModule $module): View
    {
        $module->loadMissing('course');

        Gate::authorize('update', $module->course);

        return view('admin.modules.edit', [
            'module' => $module,
            'course' => $module->course,
        ]);
    }

    public function update(CourseModuleRequest $request, CourseModule $module): RedirectResponse
    {
        $module->loadMissing('course');

        Gate::authorize('update', $module->course);

        $module->update($request->safe()->only(['title', 'description', 'sort_order']));

        return redirect()->route('admin.courses.curriculum', $module->course)
            ->with('status', 'Module updated.');
    }

    public function destroy(CourseModule $module): RedirectResponse
    {
        $course = $module->loadMissing('course')->course;

        Gate::authorize('update', $course);

        $lessonCount = $module->lessons()->count();

        // Lessons cascade with the module, so say plainly what is going.
        $module->delete();

        return redirect()->route('admin.courses.curriculum', $course)
            ->with('status', $lessonCount > 0
                ? "Module deleted along with {$lessonCount} lesson(s)."
                : 'Module deleted.');
    }
}
