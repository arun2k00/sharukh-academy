<?php

namespace App\Models;

use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'course_id', 'order_id', 'last_lesson_id', 'status', 'source',
    'progress_percent', 'enrolled_at', 'completed_at', 'expires_at', 'last_accessed_at',
])]
class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    /**
     * Mirrors the database defaults so an unsaved enrolment can still answer
     * questions about the access it would grant.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => EnrollmentStatus::Active->value,
        'source' => EnrollmentSource::Admin->value,
        'progress_percent' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
            'source' => EnrollmentSource::class,
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_accessed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<Lesson, $this>
     */
    public function lastLesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'last_lesson_id');
    }

    /**
     * Whether this enrolment currently unlocks the course.
     *
     * An expiry date in the past revokes access even if the stored status was
     * never updated, so a missed scheduled task cannot leak content.
     */
    public function grantsAccess(): bool
    {
        if (! $this->status->grantsAccess()) {
            return false;
        }

        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    public function isCompleted(): bool
    {
        return $this->status === EnrollmentStatus::Completed;
    }

    /**
     * @param  Builder<Enrollment>  $query
     * @return Builder<Enrollment>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Active);
    }

    /**
     * @param  Builder<Enrollment>  $query
     * @return Builder<Enrollment>
     */
    #[Scope]
    protected function grantingAccess(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [EnrollmentStatus::Active, EnrollmentStatus::Completed])
            ->where(fn (Builder $query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()));
    }
}
