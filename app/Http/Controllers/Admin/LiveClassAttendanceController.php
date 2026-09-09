<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use App\Services\AdminActivityLogger;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Manual attendance marking (§8). Built so a future Zoom API sync can write
 * to the same `live_class_attendances` table without any UI change.
 */
class LiveClassAttendanceController extends Controller
{
    public function edit(LiveClass $liveClass, EnrollmentService $enrollments): View
    {
        $liveClass->load('course');

        $students = $liveClass->course->students()
            ->wherePivotIn('status', ['active', 'completed'])
            ->orderBy('name')
            ->get();

        $existing = $liveClass->attendances()->pluck('status', 'user_id');

        return view('admin.live-classes.attendance', [
            'liveClass' => $liveClass,
            'students' => $students,
            'existing' => $existing,
        ]);
    }

    public function update(Request $request, LiveClass $liveClass, AdminActivityLogger $activity): RedirectResponse
    {
        $validated = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'string', 'in:present,absent'],
        ]);

        DB::transaction(function () use ($validated, $liveClass): void {
            foreach ($validated['attendance'] as $userId => $status) {
                LiveClassAttendance::updateOrCreate(
                    ['live_class_id' => $liveClass->id, 'user_id' => (int) $userId],
                    [
                        'status' => AttendanceStatus::from($status),
                        'marked_at' => now(),
                        'marked_by' => Auth::id(),
                    ],
                );
            }
        });

        $activity->log('live_class.attendance_marked', $liveClass, "Recorded attendance for \"{$liveClass->title}\"");

        return back()->with('status', 'Attendance saved.');
    }
}
