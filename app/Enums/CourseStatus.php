<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    /**
     * Tailwind classes used by the admin status badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-sand-200 text-ink-600',
            self::Published => 'bg-pine-50 text-pine-800',
            self::Archived => 'bg-ink-100 text-ink-500',
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
