<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceUploadRequest;
use App\Models\Lesson;
use App\Models\LessonResource;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class LessonResourceController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function store(ResourceUploadRequest $request, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('update', $lesson);

        $lesson->resources()->create([
            'title' => $request->string('title')->toString(),
            'sort_order' => $request->integer('sort_order'),
            ...$this->storage->store($request->file('file'), 'lesson-resources/'.$lesson->id),
        ]);

        return back()->with('status', 'Resource uploaded.');
    }

    public function destroy(LessonResource $resource): RedirectResponse
    {
        Gate::authorize('delete', $resource);

        $this->storage->delete($resource->file_path);
        $resource->delete();

        return back()->with('status', 'Resource removed.');
    }
}
