<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\Instructor;
use App\Models\Testimonial;
use App\Services\SettingsRepository;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SettingsRepository $settings): View
    {
        $featured = Course::query()
            ->published()
            ->featured()
            ->with(['instructor', 'category'])
            ->withCount(['lessons' => fn ($query) => $query->where('is_published', true)])
            ->ordered()
            ->take(3)
            ->get();

        // Fall back to the newest published courses so the homepage is never
        // empty just because nothing has been flagged as featured.
        if ($featured->isEmpty()) {
            $featured = Course::query()
                ->published()
                ->with(['instructor', 'category'])
                ->withCount(['lessons' => fn ($query) => $query->where('is_published', true)])
                ->ordered()
                ->take(3)
                ->get();
        }

        return view('public.home', [
            'settings' => $settings->all(),
            'featuredCourses' => $featured,
            'leadInstructor' => Instructor::query()->where('is_featured', true)->first()
                ?? Instructor::query()->oldest('id')->first(),
            'testimonials' => Testimonial::query()->published()->where('is_featured', true)->take(3)->get(),
            'faqs' => Faq::query()->published()->whereNull('course_id')->take(6)->get(),
            'categories' => Category::query()->orderBy('sort_order')->get(),
            'stats' => [
                'courses' => Course::query()->published()->count(),
                'students' => Enrollment::query()->distinct('user_id')->count('user_id'),
                'lessonHours' => (int) round(Course::query()->published()->sum('duration_minutes') / 60),
            ],
        ]);
    }
}
