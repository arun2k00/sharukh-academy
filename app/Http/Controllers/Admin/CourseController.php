<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use App\Services\ResourceStorageService;
use App\Support\YouTube;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Course::class);

        $status = CourseStatus::tryFrom($request->string('status')->toString());
        $showingDeleted = $request->string('status')->toString() === 'deleted';

        $courses = Course::query()
            ->when($showingDeleted, fn ($query) => $query->onlyTrashed())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->with(['instructor', 'category'])
            ->withCount(['lessons', 'enrollments'])
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'showingDeleted' => $showingDeleted,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Course::class);

        return view('admin.courses.create', [
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
        $course = new Course($request->safe()->except(['thumbnail', 'preview_video_url']));

        $this->applyMedia($course, $request);

        if ($course->status === CourseStatus::Published) {
            $course->published_at = now();
        }

        $course->save();

        return redirect()->route('admin.courses.curriculum', $course)
            ->with('status', 'Course created. Build its curriculum below.');
    }

    public function show(Course $course): RedirectResponse
    {
        return redirect()->route('admin.courses.edit', $course);
    }

    public function edit(Course $course): View
    {
        Gate::authorize('update', $course);

        return view('admin.courses.edit', [
            'course' => $course,
            ...$this->formOptions(),
        ]);
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $course->fill($request->safe()->except(['thumbnail', 'preview_video_url']));

        $this->applyMedia($course, $request);

        if ($course->status === CourseStatus::Published && $course->published_at === null) {
            $course->published_at = now();
        }

        $course->save();

        return redirect()->route('admin.courses.edit', $course)
            ->with('status', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        Gate::authorize('delete', $course);

        // Soft delete keeps enrolments, progress and certificates intact and
        // recoverable; the course simply leaves the catalogue.
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('status', 'Course deleted. Student records were kept, and it can be restored from the Deleted filter.');
    }

    /**
     * @return array{categories: Collection<int, Category>, instructors: Collection<int, Instructor>}
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
            'instructors' => Instructor::query()->orderBy('name')->get(),
        ];
    }

    private function applyMedia(Course $course, CourseRequest $request): void
    {
        if ($request->hasFile('thumbnail')) {
            $this->storage->deleteImage($course->thumbnail_path);
            $course->thumbnail_path = $this->storage->storeImage($request->file('thumbnail'), 'courses');
        }

        // Only the extracted video ID is stored, never the raw pasted URL.
        $url = $request->string('preview_video_url')->toString();
        $course->preview_video_url = $url ?: null;
        $course->preview_video_id = YouTube::id($url);
    }
}
