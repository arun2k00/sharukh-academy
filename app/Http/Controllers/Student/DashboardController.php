<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\LiveClass;
use App\Services\CurriculumService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CurriculumService $curriculum): View
    {
        $user = $request->user();

        $enrollments = Enrollment::query()
            ->where('user_id', $user->id)
            ->grantingAccess()
            ->with(['course.instructor', 'lastLesson'])
            ->orderByDesc('last_accessed_at')
            ->orderByDesc('id')
            ->get();

        $current = $enrollments->first(fn (Enrollment $enrollment): bool => ! $enrollment->isCompleted())
            ?? $enrollments->first();

        $courseIds = $enrollments->pluck('course_id');

        return view('student.dashboard', [
            'enrollments' => $enrollments,
            'currentEnrollment' => $current,
            'resumeLesson' => $current !== null
                ? $curriculum->resumeLesson($user, $current->course, $current)
                : null,
            'upcomingClasses' => LiveClass::query()
                ->whereIn('course_id', $courseIds)
                ->upcoming()
                ->with('course')
                ->take(4)
                ->get(),
            'stats' => [
                'enrolled' => $enrollments->count(),
                'completed' => $enrollments->filter(fn (Enrollment $enrollment): bool => $enrollment->isCompleted())->count(),
                'lessonsCompleted' => LessonProgress::query()
                    ->where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->count(),
                'averageProgress' => (int) round($enrollments->avg('progress_percent') ?? 0),
                'certificates' => $user->certificates()->count(),
            ],
        ]);
    }
}
