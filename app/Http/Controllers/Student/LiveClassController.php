<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

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

    public function show(LiveClass $liveClass): View
    {
        $liveClass->load(['course', 'instructor', 'resources']);

        Gate::authorize('view', $liveClass);

        return view('student.live-classes.show', [
            'liveClass' => $liveClass,
        ]);
    }
}
