<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Restricts meeting links to Zoom's own domains over HTTPS.
 *
 * Students are redirected to this value, so an arbitrary URL here would turn
 * the join route into an open redirect.
 */
class ZoomUrl implements ValidationRule
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SUFFIXES = ['zoom.us', 'zoom.com', 'zoomgov.com'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Enter a valid Zoom meeting link.');

            return;
        }

        $parts = parse_url($value);

        if ($parts === false || ($parts['scheme'] ?? null) !== 'https' || ! isset($parts['host'])) {
            $fail('The Zoom link must be a full https:// address.');

            return;
        }

        $host = mb_strtolower($parts['host']);

        foreach (self::ALLOWED_SUFFIXES as $suffix) {
            if ($host === $suffix || str_ends_with($host, '.'.$suffix)) {
                return;
            }
        }

        $fail('The meeting link must point at a Zoom domain, for example https://us02web.zoom.us/j/1234567890');
    }
}
