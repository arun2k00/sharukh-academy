<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\CurriculumService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCourseController extends Controller
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

        // Resolve the resume target once per course rather than inside the view.
        $resumeLessons = $enrollments->mapWithKeys(fn (Enrollment $enrollment): array => [
            $enrollment->course_id => $curriculum->resumeLesson($user, $enrollment->course, $enrollment),
        ]);

        return view('student.my-courses', [
            'enrollments' => $enrollments,
            'resumeLessons' => $resumeLessons,
        ]);
    }
}
