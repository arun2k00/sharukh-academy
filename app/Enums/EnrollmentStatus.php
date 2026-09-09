<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Whether this status still grants access to the course content.
     */
    public function grantsAccess(): bool
    {
        return in_array($this, [self::Active, self::Completed], true);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Active => 'bg-pine-50 text-pine-800',
            self::Completed => 'bg-ink-900 text-white',
            self::Expired => 'bg-amber-50 text-amber-800',
            self::Cancelled => 'bg-ink-100 text-ink-500',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            fn (array $carry, self $case): array => $carry + [$case->value => $case->label()],
            [],
        );
    }
}
