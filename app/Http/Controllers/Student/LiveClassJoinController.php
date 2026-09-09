<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Hands the Zoom link to an enrolled student inside the join window.
 *
 * The meeting URL is never rendered into a page, so it cannot be copied out of
 * the markup by someone who is not entitled to it.
 */
class LiveClassJoinController extends Controller
{
    public function __invoke(LiveClass $liveClass): RedirectResponse
    {
        $liveClass->load('course');

        Gate::authorize('join', $liveClass);

        return redirect()->away($liveClass->zoom_url);
    }
}
