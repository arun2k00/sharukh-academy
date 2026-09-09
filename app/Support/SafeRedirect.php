<?php

namespace App\Support;

/**
 * Validates a user-supplied redirect target is a same-site relative path
 * before it is ever used in a `redirect()` call.
 *
 * Used by the "Login to join" flow on the public live-class page, where the
 * destination arrives as a query string value and must never be allowed to
 * become an open redirect to an external host.
 */
final class SafeRedirect
{
    public static function pathOrNull(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Must start with exactly one slash — rules out "//evil.com" (a
        // protocol-relative URL) and anything carrying a scheme.
        if (! str_starts_with($value, '/') || str_starts_with($value, '//')) {
            return null;
        }

        if (str_contains($value, '://') || str_contains($value, '\\')) {
            return null;
        }

        return $value;
    }
}
