<?php

namespace App\Http\Controllers\Student;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The event-style live class experience (§3, §4). `show` is deliberately
 * public: it doubles as the shareable preview page (§5), and decides for
 * itself how much to reveal rather than gating the whole page behind auth.
 */
class LiveClassController extends Controller
{
    public function index(Request $request): View
    {
        $courseIds = Enrollment::query()
            ->where('user_id', $request->user()->id)
            ->grantingAccess()
            ->pluck('course_id');

        return view('student.live-classes.index', [
            'upcoming' => LiveClass::query()
                ->whereIn('course_id', $courseIds)
                ->upcoming()
                ->with(['course', 'instructor'])
                ->get(),
            'past' => LiveClass::query()
                ->whereIn('course_id', $courseIds)
                ->past()
                ->with(['course', 'instructor'])
                ->take(10)
                ->get(),
        ]);
    }

    public function show(Request $request, LiveClass $liveClass, EnrollmentService $enrollments): View
    {
        $liveClass->load(['course.instructor', 'instructor', 'resources']);

        $user = $request->user();
        $isEnrolled = $user !== null && $enrollments->hasAccess($user, $liveClass->course);
        $canViewFull = $isEnrolled || $user?->isAdmin() === true;

        $attendance = $canViewFull && $user !== null
            ? $liveClass->attendances()->where('user_id', $user->id)->first()
            : null;

        return view('student.live-classes.show', [
            'liveClass' => $liveClass,
            'canViewFull' => $canViewFull,
            'isEnrolled' => $isEnrolled,
            'attendance' => $attendance,
            'attendedRequired' => $attendance?->status === AttendanceStatus::Present,
        ]);
    }
}
