<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\ResourceUploadRequest;
use App\Models\LiveClass;
use App\Models\LiveClassResource;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class LiveClassResourceController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function store(ResourceUploadRequest $request, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('update', $liveClass);

        $liveClass->resources()->create([
            'title' => $request->string('title')->toString(),
            'sort_order' => $request->integer('sort_order'),
            ...$this->storage->store($request->file('file'), 'live-class-resources/'.$liveClass->id),
        ]);

        return back()->with('status', 'Material uploaded.');
    }

    public function destroy(LiveClassResource $resource): RedirectResponse
    {
        Gate::authorize('delete', $resource);

        $this->storage->delete($resource->file_path);
        $resource->delete();

        return back()->with('status', 'Material removed.');
    }
}
