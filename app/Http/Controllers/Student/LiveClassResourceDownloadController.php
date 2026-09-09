<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClassResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LiveClassResourceDownloadController extends Controller
{
    public function __invoke(LiveClassResource $resource): StreamedResponse
    {
        $resource->load('liveClass.course');

        Gate::authorize('download', $resource);

        abort_unless(Storage::disk('local')->exists($resource->file_path), 404);

        $resource->increment('downloads_count');

        return Storage::disk('local')->download($resource->file_path, $resource->original_name);
    }
}
