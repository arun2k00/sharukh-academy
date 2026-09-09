<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the whole `/admin` area.
 *
 * Guests are sent to the login screen; a signed-in student gets a 403 rather
 * than a redirect, so the admin area never confirms which URLs exist.
 */
class EnsureUserIsAdmin
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

        abort_unless($user->isAdmin(), 403, 'This area is restricted to academy staff.');

        return $next($request);
    }
}
