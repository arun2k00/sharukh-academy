<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A visitor who signed in through the public "try it" demo buttons (see
 * DemoLoginController) may look around, but can never change anything —
 * the underlying accounts are real, reused by every visitor, and one of
 * them (the seeded Trainer) owns real course content.
 *
 * The restriction lives on the session, not the account, so the same
 * `trainer@aixone.site` login still works normally for its actual owner
 * signing in the regular way through /login.
 */
class EnsureDemoAccountIsReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $isWrite = ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);

        if ($isWrite && $request->session()->get('is_demo_session') === true && ! $request->routeIs('logout')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This is a demo account for exploring the site. Changes cannot be saved.',
                ], 403);
            }

            return back()->with('error', 'This is a demo account for exploring the site. Changes cannot be saved.');
        }

        return $next($request);
    }
}
