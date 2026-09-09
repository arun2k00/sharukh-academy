<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stamps `last_login_at` at most once a day so the admin student list can show
 * recent activity without writing on every request.
 */
class RecordLastLogin
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ($user->last_login_at === null || $user->last_login_at->isBefore(now()->subHours(6)))) {
            $user->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
