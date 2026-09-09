<?php

namespace App\Models;

use App\Enums\ModuleUnlockType;
use Carbon\CarbonInterface;
use Database\Factories\CourseModuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['course_id', 'title', 'description', 'sort_order', 'unlock_type', 'unlock_after_days', 'unlock_at'])]
class CourseModule extends Model
{
    /** @use HasFactory<CourseModuleFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unlock_type' => ModuleUnlockType::class,
            'unlock_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return HasMany<Lesson, $this>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<Lesson, $this>
     */
    public function publishedLessons(): HasMany
    {
        return $this->lessons()->where('is_published', true);
    }

    /**
     * Whether this module is open to a student, given when they enrolled.
     *
     * Enforced server-side wherever a lesson is resolved for learning (see
     * `LessonPolicy` and `LearnController`) — never only hidden on the
     * frontend, so a locked module's content is never actually reachable.
     */
    public function isUnlockedFor(?CarbonInterface $enrolledAt): bool
    {
        return match ($this->unlock_type) {
            ModuleUnlockType::Immediate => true,
            ModuleUnlockType::DaysAfterEnrollment => $enrolledAt !== null
                && $this->unlock_after_days !== null
                && now()->greaterThanOrEqualTo($enrolledAt->copy()->addDays($this->unlock_after_days)),
            ModuleUnlockType::FixedDate => $this->unlock_at !== null
                && now()->greaterThanOrEqualTo($this->unlock_at->copy()->startOfDay()),
        };
    }

    /**
     * When this module unlocks for a student, or null if it already has.
     */
    public function unlocksAt(?CarbonInterface $enrolledAt): ?CarbonInterface
    {
        return match ($this->unlock_type) {
            ModuleUnlockType::Immediate => null,
            ModuleUnlockType::DaysAfterEnrollment => $enrolledAt !== null && $this->unlock_after_days !== null
                ? $enrolledAt->copy()->addDays($this->unlock_after_days)
                : null,
            ModuleUnlockType::FixedDate => $this->unlock_at,
        };
    }
}
