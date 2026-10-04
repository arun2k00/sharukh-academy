<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline browser hardening. Set here rather than in `.htaccess` so the
 * headers survive a move between web servers and hosting accounts.
 *
 * No Content-Security-Policy yet: the templates still carry inline Alpine
 * handlers and inline `<style>`, so a meaningful policy would need
 * `unsafe-inline` and protect nothing. That is a separate piece of work.
 */
class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // PHP advertises its exact version by default, which just tells a
        // scanner which CVEs to try. `expose_php` cannot be set from a
        // per-directory ini file on shared hosting, so it goes here.
        header_remove('X-Powered-By');
        $response->headers->remove('X-Powered-By');

        $response->headers->add([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
        ]);

        // HSTS only over TLS: sent on a plain-HTTP response it is ignored, and
        // on a local http:// dev server it would pin the developer's browser.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
