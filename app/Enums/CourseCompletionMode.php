<?php

namespace App\Enums;

enum CourseCompletionMode: string
{
    case AllLessons = 'all_lessons';
    case RequiredLessonsOnly = 'required_lessons';

    public function label(): string
    {
        return match ($this) {
            self::AllLessons => 'Every published lesson',
            self::RequiredLessonsOnly => 'Only lessons marked required',
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
