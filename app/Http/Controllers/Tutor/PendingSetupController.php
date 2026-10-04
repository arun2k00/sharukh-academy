<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Shown to a Trainer whose account has not yet been linked to an Instructor
 * profile from the admin panel — a normal, temporary state, not an error.
 */
class PendingSetupController extends Controller
{
    public function __invoke(): View
    {
        return view('tutor.pending');
    }
}
