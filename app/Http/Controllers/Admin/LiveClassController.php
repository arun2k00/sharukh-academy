<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LiveClassStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LiveClassRequest;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\LiveClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LiveClass::class);

        $scope = $request->string('scope', 'upcoming')->toString();

        $liveClasses = LiveClass::query()
            ->when($scope === 'past', fn ($query) => $query->past(), fn ($query) => $query->upcoming())
            ->with(['course', 'instructor'])
            ->withCount('resources')
            ->paginate(15)
            ->withQueryString();

        return view('admin.live-classes.index', [
            'liveClasses' => $liveClasses,
            'scope' => $scope,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', LiveClass::class);

        return view('admin.live-classes.create', [
            'liveClass' => new LiveClass([
                'course_id' => $request->integer('course') ?: null,
                'status' => LiveClassStatus::Scheduled,
                'duration_minutes' => 60,
                'scheduled_at' => now()->addWeek()->setTime(18, 0),
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(LiveClassRequest $request): RedirectResponse
    {
        $liveClass = LiveClass::create($request->validated());

        return redirect()->route('admin.live-classes.edit', $liveClass)
            ->with('status', 'Live class scheduled.');
    }

    public function show(LiveClass $liveClass): RedirectResponse
    {
        return redirect()->route('admin.live-classes.edit', $liveClass);
    }

    public function edit(LiveClass $liveClass): View
    {
        Gate::authorize('update', $liveClass);

        $liveClass->load('resources');

        return view('admin.live-classes.edit', [
            'liveClass' => $liveClass,
            ...$this->formOptions(),
        ]);
    }

    public function update(LiveClassRequest $request, LiveClass $liveClass): RedirectResponse
    {
        $liveClass->update($request->validated());

        return redirect()->route('admin.live-classes.edit', $liveClass)
            ->with('status', 'Live class updated.');
    }

    public function destroy(LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('delete', $liveClass);

        $liveClass->delete();

        return redirect()->route('admin.live-classes.index')
            ->with('status', 'Live class removed.');
    }

    /**
     * @return array{courses: Collection<int, Course>, instructors: Collection<int, Instructor>}
     */
    private function formOptions(): array
    {
        return [
            'courses' => Course::query()->orderBy('title')->get(),
            'instructors' => Instructor::query()->orderBy('name')->get(),
        ];
    }
}
