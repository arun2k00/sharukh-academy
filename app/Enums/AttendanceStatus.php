<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Present',
            self::Absent => 'Absent',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Present => 'bg-pine-50 text-pine-800',
            self::Absent => 'bg-ink-100 text-ink-500',
        };
    }
}
