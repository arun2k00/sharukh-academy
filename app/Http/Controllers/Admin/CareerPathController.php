<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CareerPathRequest;
use App\Models\CareerPath;
use App\Models\Course;
use App\Services\ResourceStorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Admin management for career paths (§ career paths) — an ordered, staged
 * sequence of existing courses rendered as a public roadmap page. Courses
 * themselves are managed entirely in Admin -> Courses; this controller only
 * curates which of them appear on a path, in what stage, and in what order.
 */
class CareerPathController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(): View
    {
        return view('admin.career-paths.index', [
            'careerPaths' => CareerPath::query()->withCount('courses')->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.career-paths.create', [
            'careerPath' => new CareerPath(['is_published' => true]),
            'courses' => $this->availableCourses(),
        ]);
    }

    public function store(CareerPathRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $careerPath = new CareerPath($this->pathAttributes($validated));
        $this->applyBanner($careerPath, $request);
        $careerPath->save();
        $careerPath->courses()->sync($this->coursesSyncData($validated));

        return redirect()->route('admin.career-paths.index')->with('status', 'Career path created.');
    }

    public function edit(CareerPath $careerPath): View
    {
        return view('admin.career-paths.edit', [
            'careerPath' => $careerPath->load('courses'),
            'courses' => $this->availableCourses(),
        ]);
    }

    public function update(CareerPathRequest $request, CareerPath $careerPath): RedirectResponse
    {
        $validated = $request->validated();
        $careerPath->fill($this->pathAttributes($validated));
        $this->applyBanner($careerPath, $request);
        $careerPath->save();
        $careerPath->courses()->sync($this->coursesSyncData($validated));

        return redirect()->route('admin.career-paths.index')->with('status', 'Career path updated.');
    }

    public function destroy(CareerPath $careerPath): RedirectResponse
    {
        $this->storage->deleteImage($careerPath->banner_path);
        $careerPath->delete();

        return redirect()->route('admin.career-paths.index')->with('status', 'Career path deleted.');
    }

    /**
     * @return Collection<int, Course>
     */
    private function availableCourses(): Collection
    {
        return Course::query()->published()->orderBy('title')->get();
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function pathAttributes(array $validated): array
    {
        $attributes = $validated;
        unset($attributes['courses'], $attributes['banner']);

        return $attributes;
    }

    private function applyBanner(CareerPath $careerPath, CareerPathRequest $request): void
    {
        if ($request->hasFile('banner')) {
            $this->storage->deleteImage($careerPath->banner_path);
            $careerPath->banner_path = $this->storage->storeImage($request->file('banner'), 'career-paths');
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, array{stage_label: string, sort_order: int}>
     */
    private function coursesSyncData(array $validated): array
    {
        $sync = [];

        foreach ($validated['courses'] ?? [] as $index => $row) {
            $sync[(int) $row['course_id']] = [
                'stage_label' => $row['stage_label'],
                'sort_order' => $row['sort_order'] ?? $index,
            ];
        }

        return $sync;
    }
}
