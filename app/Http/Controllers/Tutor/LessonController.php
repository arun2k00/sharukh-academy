<?php

namespace App\Http\Controllers\Tutor;

use App\Enums\LessonType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\LessonRequest;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Services\CourseMetricsService;
use App\Services\CurriculumOrderService;
use App\Support\YouTube;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function __construct(
        private readonly CurriculumOrderService $ordering,
        private readonly CourseMetricsService $metrics,
    ) {}

    public function create(Request $request, Course $course): View
    {
        Gate::authorize('update', $course);

        $lesson = new Lesson([
            'type' => LessonType::Video,
            'course_module_id' => $request->integer('module') ?: $course->modules()->value('id'),
            'duration_minutes' => 0,
            'is_published' => true,
        ]);

        return view('tutor.lessons.create', [
            'course' => $course,
            'lesson' => $lesson,
            ...$this->formOptions($course),
        ]);
    }

    public function store(LessonRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('update', $course);

        $lesson = new Lesson($request->safe()->except(['sort_order']));
        $lesson->course_id = $course->id;
        $lesson->sort_order = $request->integer('sort_order')
            ?: $this->ordering->nextPositionForLesson((int) $request->input('course_module_id'));

        $this->applyTypeFields($lesson, $request);
        $lesson->save();

        $this->metrics->refresh($course);

        return redirect()->route('tutor.courses.curriculum', $course)
            ->with('status', 'Lesson added.');
    }

    public function edit(Lesson $lesson): View
    {
        Gate::authorize('update', $lesson);

        $lesson->load(['resources', 'course']);
        $course = $lesson->course;

        return view('tutor.lessons.edit', [
            'course' => $course,
            'lesson' => $lesson,
            ...$this->formOptions($course),
        ]);
    }

    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson);

        $lesson->fill($request->safe()->all());

        $this->applyTypeFields($lesson, $request);
        $lesson->save();

        $this->metrics->refresh($lesson->loadMissing('course')->course);

        return redirect()->route('tutor.lessons.edit', $lesson)
            ->with('status', 'Lesson updated.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        Gate::authorize('delete', $lesson);

        $course = $lesson->loadMissing('course')->course;
        $lesson->delete();

        $this->metrics->refresh($course);

        return redirect()->route('tutor.courses.curriculum', $course)
            ->with('status', 'Lesson deleted.');
    }

    /**
     * @return array{modules: Collection<int, CourseModule>, liveClasses: Collection<int, LiveClass>}
     */
    private function formOptions(Course $course): array
    {
        return [
            'modules' => $course->modules()->get(),
            'liveClasses' => $course->liveClasses()->orderByDesc('scheduled_at')->get(),
        ];
    }

    private function applyTypeFields(Lesson $lesson, LessonRequest $request): void
    {
        $lesson->video_id = $lesson->type === LessonType::Video
            ? YouTube::id($request->string('video_url')->toString())
            : null;

        if ($lesson->type !== LessonType::Video) {
            $lesson->video_url = null;
        }

        if ($lesson->type !== LessonType::Live) {
            $lesson->live_class_id = null;
        }
    }
}
