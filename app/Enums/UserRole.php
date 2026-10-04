<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Trainer = 'trainer';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Trainer => 'Trainer',
            self::Student => 'Student',
        };
    }
}
