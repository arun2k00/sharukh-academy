<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Faq;
use App\Services\EnrollmentService;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount(['courses' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Category $category): bool => $category->courses_count > 0);

        $activeCategory = $categories->firstWhere('slug', $request->string('category')->toString());
        $search = $request->string('q')->trim()->toString();

        $courses = Course::query()
            ->published()
            ->when($activeCategory, fn ($query) => $query->where('category_id', $activeCategory->id))
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->with(['instructor', 'category'])
            ->withCount(['lessons' => fn ($query) => $query->where('is_published', true)])
            ->ordered()
            ->get();

        return view('public.courses.index', [
            'courses' => $courses,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'search' => $search,
        ]);
    }

    public function show(
        Request $request,
        Course $course,
        EnrollmentService $enrollments,
        PaymentManager $payments,
    ): View {
        Gate::authorize('view', $course);

        $course->load([
            'instructor',
            'category',
            'testimonials' => fn ($query) => $query->published()->take(3),
            'careerPaths' => fn ($query) => $query->published(),
        ]);

        $curriculum = $course->curriculum();
        $user = $request->user();

        $reviews = CourseReview::query()
            ->where('course_id', $course->id)
            ->approved()
            ->with('user')
            ->latest('id')
            ->get();

        return view('public.courses.show', [
            'course' => $course,
            'curriculum' => $curriculum,
            'lessonCount' => $curriculum->sum(fn ($module) => $module->lessons->count()),
            'liveClassCount' => $course->liveClasses()->whereNot('status', 'cancelled')->count(),
            'resourceCount' => $curriculum->sum(fn ($module) => $module->lessons->sum('resources_count')),
            'enrollment' => $user !== null ? $enrollments->find($user, $course) : null,
            'hasAccess' => $user !== null && $enrollments->hasAccess($user, $course),
            'paymentsOnline' => $payments->processesPaymentsOnline(),
            'availableGateways' => $payments->availableOnlineDrivers(),
            'reviews' => $reviews,
            'averageRating' => $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : null,
            'myReview' => $user !== null ? CourseReview::where('course_id', $course->id)->where('user_id', $user->id)->first() : null,
            'faqs' => Faq::query()
                ->published()
                ->where(fn ($query) => $query->where('course_id', $course->id)->orWhereNull('course_id'))
                ->orderByRaw('course_id is null')
                ->take(6)
                ->get(),
        ]);
    }
}
