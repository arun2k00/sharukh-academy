<?php

namespace App\Models;

use App\Enums\LessonType;
use App\Support\Duration;
use App\Support\YouTube;
use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'course_id', 'course_module_id', 'live_class_id', 'title', 'slug', 'type',
    'description', 'content', 'video_url', 'video_id', 'duration_minutes',
    'sort_order', 'is_preview', 'is_published', 'is_required',
])]
class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LessonType::class,
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
            'is_required' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<CourseModule, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    /**
     * @return BelongsTo<LiveClass, $this>
     */
    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }

    /**
     * @return HasMany<LessonResource, $this>
     */
    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<LessonProgress, $this>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * A preview lesson is watchable by anyone, enrolled or not.
     */
    public function isFreePreview(): bool
    {
        return $this->is_preview && $this->is_published;
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function embedUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => YouTube::embedUrl($this->video_id));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function durationLabel(): Attribute
    {
        return Attribute::get(fn (): string => Duration::label($this->duration_minutes));
    }

    /**
     * @param  Builder<Lesson>  $query
     * @return Builder<Lesson>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Curriculum order across a whole course: module position, then lesson position.
     *
     * @param  Builder<Lesson>  $query
     * @return Builder<Lesson>
     */
    #[Scope]
    protected function inCurriculumOrder(Builder $query): Builder
    {
        return $query
            ->join('course_modules', 'course_modules.id', '=', 'lessons.course_module_id')
            ->orderBy('course_modules.sort_order')
            ->orderBy('course_modules.id')
            ->orderBy('lessons.sort_order')
            ->orderBy('lessons.id')
            ->select('lessons.*');
    }
}
