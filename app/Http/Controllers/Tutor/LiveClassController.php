<?php

namespace App\Http\Controllers\Tutor;

use App\Enums\LiveClassStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\LiveClassRequest;
use App\Models\Course;
use App\Models\LiveClass;
use App\Services\AdminActivityLogger;
use App\Services\NotificationDispatcher;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * A Trainer's own live classes. `instructor_id` (both the LiveClass's own
 * attribution and the course picker) is always the acting Trainer's linked
 * Instructor — never a free choice from every instructor/course in the
 * system, unlike Admin\LiveClassController.
 */
class LiveClassController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LiveClass::class);

        $instructor = $request->user()->instructor;
        $scope = $request->string('scope', 'upcoming')->toString();

        $liveClasses = LiveClass::query()
            ->where('instructor_id', $instructor->id)
            ->when($scope === 'past', fn ($query) => $query->past(), fn ($query) => $query->upcoming())
            ->with('course')
            ->withCount('resources')
            ->paginate(15)
            ->withQueryString();

        return view('tutor.live-classes.index', [
            'liveClasses' => $liveClasses,
            'scope' => $scope,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', LiveClass::class);

        return view('tutor.live-classes.create', [
            'liveClass' => new LiveClass([
                'course_id' => $request->integer('course') ?: null,
                'status' => LiveClassStatus::Scheduled,
                'duration_minutes' => 60,
                'scheduled_at' => now()->addWeek()->setTime(18, 0),
            ]),
            ...$this->formOptions($request),
        ]);
    }

    public function store(
        LiveClassRequest $request,
        NotificationDispatcher $notifications,
        AdminActivityLogger $activity,
    ): RedirectResponse {
        $liveClass = new LiveClass($request->safe()->except('banner'));
        $liveClass->instructor_id = $request->user()->instructor->id;
        $this->applyBanner($liveClass, $request);
        $liveClass->save();

        $notifications->newLiveClassScheduled($liveClass);
        $activity->log('tutor.live_class.created', $liveClass, "Scheduled live class \"{$liveClass->title}\"");

        return redirect()->route('tutor.live-classes.edit', $liveClass)
            ->with('status', 'Live class scheduled. Enrolled students have been notified.');
    }

    public function show(LiveClass $liveClass): RedirectResponse
    {
        return redirect()->route('tutor.live-classes.edit', $liveClass);
    }

    public function edit(Request $request, LiveClass $liveClass): View
    {
        Gate::authorize('update', $liveClass);

        $liveClass->load('resources');

        return view('tutor.live-classes.edit', [
            'liveClass' => $liveClass,
            ...$this->formOptions($request),
        ]);
    }

    public function update(
        LiveClassRequest $request,
        LiveClass $liveClass,
        NotificationDispatcher $notifications,
        AdminActivityLogger $activity,
    ): RedirectResponse {
        $hadNoRecording = blank($liveClass->recording_url);

        $liveClass->fill($request->safe()->except('banner'));
        $this->applyBanner($liveClass, $request);
        $liveClass->save();

        if ($hadNoRecording && filled($liveClass->recording_url)) {
            $notifications->recordingAvailable($liveClass);
        }

        $activity->log('tutor.live_class.updated', $liveClass, "Updated live class \"{$liveClass->title}\"");

        return redirect()->route('tutor.live-classes.edit', $liveClass)
            ->with('status', 'Live class updated.');
    }

    public function destroy(LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('delete', $liveClass);

        $liveClass->delete();

        return redirect()->route('tutor.live-classes.index')
            ->with('status', 'Live class removed.');
    }

    /**
     * @return array{courses: Collection<int, Course>}
     */
    private function formOptions(Request $request): array
    {
        return [
            'courses' => Course::query()->where('instructor_id', $request->user()->instructor->id)->orderBy('title')->get(),
        ];
    }

    private function applyBanner(LiveClass $liveClass, LiveClassRequest $request): void
    {
        if ($request->hasFile('banner')) {
            $this->storage->deleteImage($liveClass->banner_path);
            $liveClass->banner_path = $this->storage->storeImage($request->file('banner'), 'live-classes/banners');
        }
    }
}
