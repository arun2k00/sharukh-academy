<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\CertificateService;
use App\Services\CurriculumService;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LearnController extends Controller
{
    public function __construct(
        private readonly CurriculumService $curriculum,
        private readonly ProgressService $progress,
        private readonly EnrollmentService $enrollments,
    ) {}

    /**
     * Entering a course sends the student straight to where they left off.
     */
    public function show(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('learn', $course);

        $lesson = $this->curriculum->resumeLesson($request->user(), $course);

        if ($lesson === null) {
            return redirect()->route('my-courses')
                ->with('error', 'This course does not have any published lessons yet.');
        }

        return redirect()->route('learn.lesson', [$course, $lesson]);
    }

    public function lesson(
        Request $request,
        Course $course,
        Lesson $lesson,
        CertificateService $certificates,
    ): View {
        Gate::authorize('learn', $course);
        abort_unless($lesson->is_published, 404);

        $user = $request->user();

        $lesson->load(['resources', 'liveClass', 'module']);
        $course->load('instructor');

        $enrollment = $this->enrollments->accessFor($user, $course);

        if ($enrollment !== null) {
            $this->enrollments->recordAccess($enrollment, $lesson);
            $this->progress->touchViewed($user, $lesson);
        }

        return view('student.learn.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'enrollment' => $enrollment,
            'curriculum' => $course->curriculum(),
            'completedMap' => $this->progress->completedLessonMap($user, $course),
            'progressPercent' => $this->progress->percentFor($user, $course),
            'previousLesson' => $this->curriculum->previousLesson($course, $lesson),
            'nextLesson' => $this->curriculum->nextLesson($course, $lesson),
            'certificate' => $certificates->find($user, $course),
            'watchedPercent' => (int) LessonProgress::query()
                ->where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->value('watched_percent'),
        ]);
    }
}
