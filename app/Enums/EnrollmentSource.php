<?php

namespace App\Enums;

enum EnrollmentSource: string
{
    case Admin = 'admin';
    case Free = 'free';
    case Purchase = 'purchase';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Enrolled by admin',
            self::Free => 'Free enrolment',
            self::Purchase => 'Purchase',
        };
    }
}
