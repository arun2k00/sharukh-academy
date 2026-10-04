<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the whole `/tutor` area.
 *
 * Guests are sent to the login screen; a signed-in non-trainer gets a 403
 * rather than a redirect, so the area never confirms which URLs exist. A
 * trainer whose account has not yet been linked to an Instructor profile
 * (an admin panel step) is sent to a holding page instead of a 403, since
 * that is a normal, temporary state rather than a permissions violation.
 */
class EnsureUserIsTrainer
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        abort_unless($user->isTrainer(), 403, 'This area is restricted to academy trainers.');

        $user->loadMissing('instructor');

        if ($user->instructor === null && ! $request->routeIs('tutor.pending')) {
            return redirect()->route('tutor.pending');
        }

        return $next($request);
    }
}
