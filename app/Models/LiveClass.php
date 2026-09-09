<?php

namespace App\Models;

use App\Enums\LiveClassStatus;
use Carbon\CarbonInterface;
use Database\Factories\LiveClassFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'course_id', 'instructor_id', 'title', 'slug', 'description', 'preparation_notes',
    'scheduled_at', 'duration_minutes', 'zoom_url', 'zoom_meeting_id', 'zoom_passcode',
    'recording_url', 'status',
])]
class LiveClass extends Model
{
    /** @use HasFactory<LiveClassFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Minutes after a class ends during which the join link still works.
     */
    private const JOIN_GRACE_MINUTES = 30;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'status' => LiveClassStatus::class,
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
     * @return BelongsTo<Instructor, $this>
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * @return HasMany<LiveClassResource, $this>
     */
    public function resources(): HasMany
    {
        return $this->hasMany(LiveClassResource::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<Lesson, $this>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * @return Attribute<CarbonInterface, never>
     */
    protected function endsAt(): Attribute
    {
        return Attribute::get(fn (): CarbonInterface => $this->scheduled_at->copy()
            ->addMinutes($this->duration_minutes));
    }

    public function hasStarted(): bool
    {
        return now()->greaterThanOrEqualTo($this->scheduled_at);
    }

    public function hasEnded(): bool
    {
        return now()->greaterThan($this->ends_at);
    }

    /**
     * The join link opens shortly before the start time and closes after a
     * short grace period so late arrivals are not locked out.
     */
    public function isJoinable(): bool
    {
        if ($this->status === LiveClassStatus::Cancelled || blank($this->zoom_url)) {
            return false;
        }

        $opensAt = $this->scheduled_at->copy()->subMinutes((int) config('academy.live_join_window', 15));
        $closesAt = $this->ends_at->copy()->addMinutes(self::JOIN_GRACE_MINUTES);

        return now()->betweenIncluded($opensAt, $closesAt);
    }

    public function isUpcoming(): bool
    {
        return ! $this->hasEnded() && $this->status !== LiveClassStatus::Cancelled;
    }

    /**
     * @param  Builder<LiveClass>  $query
     * @return Builder<LiveClass>
     */
    #[Scope]
    protected function upcoming(Builder $query): Builder
    {
        return $query->whereNot('status', LiveClassStatus::Cancelled)
            ->where('scheduled_at', '>=', now()->subHours(3))
            ->orderBy('scheduled_at');
    }

    /**
     * @param  Builder<LiveClass>  $query
     * @return Builder<LiveClass>
     */
    #[Scope]
    protected function past(Builder $query): Builder
    {
        return $query->where('scheduled_at', '<', now()->subHours(3))
            ->orderByDesc('scheduled_at');
    }
}
