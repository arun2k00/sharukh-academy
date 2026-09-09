<?php

namespace App\Enums;

enum LessonType: string
{
    case Video = 'video';
    case Live = 'live';
    case Resource = 'resource';
    case Text = 'text';
    case Quiz = 'quiz';

    public function label(): string
    {
        return match ($this) {
            self::Video => 'Recorded video',
            self::Live => 'Live class',
            self::Resource => 'Resource',
            self::Text => 'Text lesson',
            self::Quiz => 'Quiz',
        };
    }

    /**
     * Short label used inside the curriculum sidebar.
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::Video => 'Video',
            self::Live => 'Live',
            self::Resource => 'Files',
            self::Text => 'Reading',
            self::Quiz => 'Quiz',
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
