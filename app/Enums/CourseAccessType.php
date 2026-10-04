<?php

namespace App\Enums;

enum CourseAccessType: string
{
    case Lifetime = 'lifetime';
    case LimitedDuration = 'limited_duration';
    case FixedExpiry = 'fixed_expiry';

    public function label(): string
    {
        return match ($this) {
            self::Lifetime => 'Lifetime access',
            self::LimitedDuration => 'Limited duration from enrollment',
            self::FixedExpiry => 'Fixed expiry date',
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
