<?php

namespace App\Enums;

enum ModuleUnlockType: string
{
    case Immediate = 'immediate';
    case DaysAfterEnrollment = 'days_after_enrollment';
    case FixedDate = 'fixed_date';

    public function label(): string
    {
        return match ($this) {
            self::Immediate => 'Available immediately',
            self::DaysAfterEnrollment => 'Unlock a number of days after enrolment',
            self::FixedDate => 'Unlock on a fixed date',
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
