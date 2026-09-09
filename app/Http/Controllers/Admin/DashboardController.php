<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'students' => User::query()->students()->count(),
                'publishedCourses' => Course::query()->where('status', CourseStatus::Published)->count(),
                'draftCourses' => Course::query()->where('status', CourseStatus::Draft)->count(),
                'enrollments' => Enrollment::query()->count(),
                'certificates' => Certificate::query()->count(),
                'unreadMessages' => ContactMessage::query()->where('is_read', false)->count(),
            ],
            'recentEnrollments' => Enrollment::query()
                ->with(['user', 'course'])
                ->latest('id')
                ->take(8)
                ->get(),
            'recentStudents' => User::query()
                ->students()
                ->withCount('enrollments')
                ->latest('id')
                ->take(6)
                ->get(),
            'upcomingClasses' => LiveClass::query()
                ->upcoming()
                ->with('course')
                ->take(5)
                ->get(),
            'topCourses' => Course::query()
                ->withCount('enrollments')
                ->orderByDesc('enrollments_count')
                ->take(5)
                ->get(),
        ]);
    }
}
