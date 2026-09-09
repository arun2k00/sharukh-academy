<?php

namespace App\Http\Controllers;

use App\Models\CareerPath;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Public roadmap pages (§ career paths): a curated, staged sequence of
 * existing courses. Enrolling still happens on each course's own page —
 * this is a guide through the catalogue, not a separate purchase flow.
 */
class CareerPathController extends Controller
{
    public function index(): View
    {
        return view('public.career-paths.index', [
            'careerPaths' => CareerPath::query()->published()->ordered()->withCount('courses')->get(),
        ]);
    }

    public function show(Request $request, CareerPath $careerPath, EnrollmentService $enrollments): View
    {
        Gate::authorize('view', $careerPath);

        $careerPath->load(['courses' => function ($query): void {
            $query->with(['instructor', 'category'])->withCount(['lessons' => fn ($q) => $q->where('is_published', true)]);
        }]);

        $user = $request->user();

        return view('public.career-paths.show', [
            'careerPath' => $careerPath,
            'stages' => $careerPath->stages(),
            'accessByCourseId' => $user === null
                ? []
                : $careerPath->courses->mapWithKeys(fn ($course) => [$course->id => $enrollments->hasAccess($user, $course)]),
        ]);
    }
}
