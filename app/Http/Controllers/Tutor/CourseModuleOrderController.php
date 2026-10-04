<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Services\CurriculumOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseModuleOrderController extends Controller
{
    public function __invoke(Request $request, CourseModule $module, CurriculumOrderService $ordering): RedirectResponse
    {
        Gate::authorize('update', $module->loadMissing('course')->course);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $ordering->moveModule($module, $validated['direction']);

        return back()->with('status', 'Module order updated.');
    }
}
