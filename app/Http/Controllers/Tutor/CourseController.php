<?php

namespace App\Http\Controllers\Tutor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\CourseRequest;
use App\Models\Category;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Services\ResourceStorageService;
use App\Support\YouTube;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * A Trainer's own courses. Forked from Admin\CourseController — the
 * difference throughout is that `instructor_id` is never a request field; it
 * is always the acting Trainer's own linked Instructor, forced server-side.
 */
class CourseController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(Request $request): View
    {
        $instructor = $request->user()->instructor;

        $courses = Course::query()
            ->where('instructor_id', $instructor->id)
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->with('category')
            ->withCount(['lessons', 'enrollments'])
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('tutor.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Course::class);

        return view('tutor.courses.create', [
            'course' => new Course([
                'status' => CourseStatus::Draft,
                'level' => 'beginner',
                'language' => 'English',
                'price' => 0,
                'sort_order' => 0,
                'certificate_enabled' => true,
                'completion_threshold' => config('academy.certificate_threshold'),
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(CourseRequest $request): RedirectResponse
    {
        $course = new Course($request->safe()->except(['thumbnail', 'banner', 'preview_video_url']));
        $course->instructor_id = $request->user()->instructor->id;

        $this->applyMedia($course, $request);

        if ($course->status === CourseStatus::Published) {
            $course->published_at = now();
        }

        $course->save();

        return redirect()->route('tutor.courses.curriculum', $course)
            ->with('status', 'Course created. Build its curriculum below.');
    }

    public function show(Course $course): RedirectResponse
    {
        return redirect()->route('tutor.courses.edit', $course);
    }

    public function edit(Course $course): View
    {
        Gate::authorize('update', $course);

        return view('tutor.courses.edit', [
            'course' => $course,
            ...$this->formOptions(),
        ]);
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $course->fill($request->safe()->except(['thumbnail', 'banner', 'preview_video_url']));

        $this->applyMedia($course, $request);

        if ($course->status === CourseStatus::Published && $course->published_at === null) {
            $course->published_at = now();
        }

        $course->save();

        return redirect()->route('tutor.courses.edit', $course)
            ->with('status', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);

        // Soft delete keeps enrolments, progress and certificates intact.
        // Only an administrator can restore a deleted course.
        $course->delete();

        return redirect()->route('tutor.courses.index')
            ->with('status', 'Course deleted. Contact an administrator if you need it restored.');
    }

    /**
     * @return array{categories: Collection<int, Category>, certificateTemplates: Collection<int, CertificateTemplate>}
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
            'certificateTemplates' => CertificateTemplate::query()->orderByDesc('is_active')->orderBy('name')->get(),
        ];
    }

    private function applyMedia(Course $course, CourseRequest $request): void
    {
        if ($request->hasFile('thumbnail')) {
            $this->storage->deleteImage($course->thumbnail_path);
            $course->thumbnail_path = $this->storage->storeImage($request->file('thumbnail'), 'courses');
        }

        if ($request->hasFile('banner')) {
            $this->storage->deleteImage($course->banner_path);
            $course->banner_path = $this->storage->storeImage($request->file('banner'), 'courses/banners');
        }

        $url = $request->string('preview_video_url')->toString();
        $course->preview_video_url = $url ?: null;
        $course->preview_video_id = YouTube::id($url);
    }
}
