<?php

namespace App\Enums;

enum AccountType: string
{
    case Free = 'free';
    case Pro = 'pro';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Pro => 'Pro',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Free => 'bg-ink-100 text-ink-600',
            self::Pro => 'bg-pine-50 text-pine-800',
        };
    }
}
