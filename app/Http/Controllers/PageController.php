<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\Testimonial;
use App\Services\SettingsRepository;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('public.about', [
            'testimonials' => Testimonial::query()->published()->take(4)->get(),
            'stats' => [
                'courses' => Course::query()->published()->count(),
                'students' => Enrollment::query()->distinct('user_id')->count('user_id'),
                'lessonHours' => (int) round(Course::query()->published()->sum('duration_minutes') / 60),
            ],
        ]);
    }

    public function contact(SettingsRepository $settings): View
    {
        return view('public.contact', [
            'settings' => $settings->all(),
        ]);
    }

    public function faculty(): View
    {
        return view('public.faculty', [
            'instructors' => Instructor::query()
                ->withCount(['courses' => fn ($query) => $query->published()])
                ->orderByDesc('is_featured')
                ->orderBy('id')
                ->get(),
        ]);
    }
}
