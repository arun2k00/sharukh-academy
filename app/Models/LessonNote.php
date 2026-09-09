<?php

namespace App\Models;

use Database\Factories\LessonNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'lesson_id', 'course_id', 'content', 'video_timestamp_seconds'])]
class LessonNote extends Model
{
    /** @use HasFactory<LessonNoteFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Lesson, $this>
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * mm:ss label for the optional video moment this note refers to.
     *
     * @return Attribute<string|null, never>
     */
    protected function timestampLabel(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->video_timestamp_seconds === null) {
                return null;
            }

            return sprintf('%d:%02d', intdiv($this->video_timestamp_seconds, 60), $this->video_timestamp_seconds % 60);
        });
    }
}
