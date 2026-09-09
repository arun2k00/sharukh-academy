<?php

namespace App\Rules;

use App\Support\YouTube;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepts only values a video ID can actually be extracted from.
 */
class YouTubeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! YouTube::isValid($value)) {
            $fail('Enter a valid YouTube video link, for example https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        }
    }
}
