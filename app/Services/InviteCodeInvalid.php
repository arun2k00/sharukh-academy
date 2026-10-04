<?php

namespace App\Services;

use RuntimeException;

class InviteCodeInvalid extends RuntimeException
{
    public static function notFound(): self
    {
        return new self('That invite code was not found for this course.');
    }

    public static function revoked(): self
    {
        return new self('That invite code has been revoked.');
    }

    public static function expired(): self
    {
        return new self('That invite code has expired.');
    }

    public static function exhausted(): self
    {
        return new self('That invite code has already reached its maximum number of uses.');
    }
}
