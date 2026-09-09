<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EnrollmentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Enrollment::class);

        $status = EnrollmentStatus::tryFrom($request->string('status')->toString());

        $enrollments = Enrollment::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($request->integer('course'), fn ($query, $courseId) => $query->where('course_id', $courseId))
            ->when($request->filled('q'), fn ($query) => $query->whereHas(
                'user',
                fn ($user) => $user->search($request->string('q')->toString()),
            ))
            ->with(['user', 'course'])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.enrollments.index', [
            'enrollments' => $enrollments,
            'courses' => Course::query()->orderBy('title')->get(),
            'activeStatus' => $status,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Enrollment::class);

        return view('admin.enrollments.create', [
            'students' => User::query()->students()->orderBy('name')->get(),
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    public function store(EnrollmentRequest $request): RedirectResponse
    {
        $student = User::findOrFail($request->integer('user_id'));
        $course = Course::findOrFail($request->integer('course_id'));

        $enrollment = $this->enrollments->enroll($student, $course, EnrollmentSource::Admin);

        $this->enrollments->changeStatus($enrollment, EnrollmentStatus::from($request->string('status')->toString()));

        $enrollment->forceFill(['expires_at' => $request->date('expires_at')])->save();

        return redirect()->route('admin.enrollments.index')
            ->with('status', $student->name.' now has access to '.$course->title.'.');
    }

    public function update(EnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $this->enrollments->changeStatus(
            $enrollment,
            EnrollmentStatus::from($request->string('status')->toString()),
        );

        $enrollment->forceFill(['expires_at' => $request->date('expires_at')])->save();

        return back()->with('status', 'Enrolment updated.');
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        Gate::authorize('delete', $enrollment);

        $enrollment->delete();

        return back()->with('status', 'Enrolment removed. The student can no longer open the course.');
    }
}
