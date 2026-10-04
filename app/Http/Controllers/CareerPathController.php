<?php

namespace App\Http\Controllers;

use App\Enums\CourseAccessType;
use App\Models\CareerPath;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $careerPaths = CareerPath::query()
            ->published()
            ->ordered()
            ->withCount('courses')
            ->with(['courses' => fn ($query) => $query->orderBy('career_path_course.sort_order')->take(1)])
            ->get();

        return view('public.career-paths.index', [
            'careerPaths' => $careerPaths,
        ]);
    }

    public function show(Request $request, CareerPath $careerPath, EnrollmentService $enrollments): View
    {
        Gate::authorize('view', $careerPath);

        $careerPath->load(['courses' => function ($query): void {
            $query->with(['instructor', 'category'])->withCount(['lessons' => fn ($q) => $q->where('is_published', true)]);
        }]);

        $user = $request->user();
        $courseIds = $careerPath->courses->pluck('id');

        $instructors = $careerPath->courses->pluck('instructor')->filter()->unique('id')->values();

        $testimonials = Testimonial::query()->published()->whereIn('course_id', $courseIds)->take(6)->get();
        $faqs = Faq::query()->published()->whereIn('course_id', $courseIds)->take(6)->get();

        if ($faqs->isEmpty()) {
            $faqs = Faq::query()->published()->whereNull('course_id')->take(6)->get();
        }

        $individualTotal = (float) $careerPath->courses->sum('price');
        $bundlePrice = $careerPath->bundleCourse?->price !== null ? (float) $careerPath->bundleCourse->price : null;

        return view('public.career-paths.show', [
            'careerPath' => $careerPath,
            'stages' => $careerPath->stages(),
            'accessByCourseId' => $user === null
                ? []
                : $careerPath->courses->mapWithKeys(fn ($course) => [$course->id => $enrollments->hasAccess($user, $course)]),
            'instructors' => $instructors,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'outcomes' => $this->uniqueLines($careerPath->courses->pluck('outcomes')),
            'audience' => $this->uniqueLines($careerPath->courses->pluck('audience')),
            'requirements' => $this->uniqueLines($careerPath->courses->pluck('requirements')),
            'allLifetimeAccess' => $careerPath->courses->every(fn ($course) => $course->access_type === CourseAccessType::Lifetime),
            'stats' => [
                'courses' => $careerPath->courses->count(),
                'hours' => (int) round($careerPath->courses->sum('duration_minutes') / 60),
                'students' => Enrollment::query()->whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id'),
                'individualTotal' => $individualTotal,
                'bundlePrice' => $bundlePrice,
                'savingsPercent' => $bundlePrice !== null && $individualTotal > 0
                    ? (int) round((1 - $bundlePrice / $individualTotal) * 100)
                    : null,
            ],
        ]);
    }

    /**
     * Flatten each course's line-array field (outcomes/audience/requirements)
     * into one deduplicated list, capped so the page stays scannable rather
     * than repeating near-identical lines from every course in the path.
     *
     * @param  Collection<int, array<int, string>|null>  $lists
     * @return list<string>
     */
    private function uniqueLines(Collection $lists): array
    {
        return $lists->filter()->flatten()->unique()->take(8)->values()->all();
    }
}
