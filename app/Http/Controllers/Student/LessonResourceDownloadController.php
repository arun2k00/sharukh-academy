<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a lesson attachment after checking enrolment.
 *
 * Files are stored outside the web root, so this controller is the only way to
 * reach one — a stored path is never guessable or directly requestable.
 */
class LessonResourceDownloadController extends Controller
{
    public function __invoke(LessonResource $resource): StreamedResponse
    {
        $resource->load('lesson.course');

        Gate::authorize('download', $resource);

        abort_unless(Storage::disk('local')->exists($resource->file_path), 404);

        $resource->increment('downloads_count');

        return Storage::disk('local')->download($resource->file_path, $resource->original_name);
    }
}
