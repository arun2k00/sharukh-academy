<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InstructorRequest;
use App\Models\Instructor;
use App\Models\User;
use App\Services\ResourceStorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(): View
    {
        return view('admin.instructors.index', [
            'instructors' => Instructor::query()->withCount('courses')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.instructors.create', [
            'instructor' => new Instructor,
            'trainers' => $this->trainerOptions(),
        ]);
    }

    public function store(InstructorRequest $request): RedirectResponse
    {
        $instructor = new Instructor($request->safe()->except('avatar'));

        if ($request->hasFile('avatar')) {
            $instructor->avatar_path = $this->storage->storeImage($request->file('avatar'), 'instructors');
        }

        $instructor->save();

        return redirect()->route('admin.instructors.index')->with('status', 'Instructor added.');
    }

    public function edit(Instructor $instructor): View
    {
        return view('admin.instructors.edit', [
            'instructor' => $instructor,
            'trainers' => $this->trainerOptions(),
        ]);
    }

    public function update(InstructorRequest $request, Instructor $instructor): RedirectResponse
    {
        $instructor->fill($request->safe()->except('avatar'));

        if ($request->hasFile('avatar')) {
            $this->storage->deleteImage($instructor->avatar_path);
            $instructor->avatar_path = $this->storage->storeImage($request->file('avatar'), 'instructors');
        }

        $instructor->save();

        return redirect()->route('admin.instructors.index')->with('status', 'Instructor updated.');
    }

    public function destroy(Instructor $instructor): RedirectResponse
    {
        $this->storage->deleteImage($instructor->avatar_path);
        $instructor->delete();

        return redirect()->route('admin.instructors.index')->with('status', 'Instructor removed.');
    }

    /**
     * @return Collection<int, User>
     */
    private function trainerOptions(): Collection
    {
        return User::query()->trainers()->orderBy('name')->get();
    }
}
