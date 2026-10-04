<?php

namespace App\Http\Controllers\Tutor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Aggregate counts only — courses by status, total enrollments across owned
 * courses, upcoming live classes. Deliberately no student roster or
 * per-student progress view: that would expose student data to a new
 * audience, which wasn't asked for.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $instructor = $request->user()->instructor;

        $courses = $instructor->courses()->withCount('enrollments')->get();

        return view('tutor.dashboard', [
            'instructor' => $instructor,
            'courses' => $courses,
            'publishedCount' => $courses->where('status', CourseStatus::Published)->count(),
            'draftCount' => $courses->where('status', CourseStatus::Draft)->count(),
            'totalEnrollments' => $courses->sum('enrollments_count'),
            'upcomingLiveClasses' => $instructor->liveClasses()->with('course')->upcoming()->orderBy('scheduled_at')->take(5)->get(),
        ]);
    }
}
