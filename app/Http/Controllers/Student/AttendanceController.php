<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "My Attendance" (§8): a read-only record of past live classes and whether
 * the student was marked present.
 */
class AttendanceController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $courseIds = $user->enrollments()->grantingAccess()->pluck('course_id');

        $classes = LiveClass::query()
            ->whereIn('course_id', $courseIds)
            ->where('scheduled_at', '<', now())
            ->with(['course', 'attendances' => fn ($query) => $query->where('user_id', $user->id)])
            ->orderByDesc('scheduled_at')
            ->get();

        return view('student.attendance.index', ['classes' => $classes]);
    }
}
