<?php

namespace App\Enums;

enum EnrollmentSource: string
{
    case Admin = 'admin';
    case Free = 'free';
    case Purchase = 'purchase';
    case Invite = 'invite';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Enrolled by admin',
            self::Free => 'Free enrollment',
            self::Purchase => 'Purchase',
            self::Invite => 'Invite code',
        };
    }
}
