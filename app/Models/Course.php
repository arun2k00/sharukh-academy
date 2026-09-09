<?php

namespace App\Models;

use App\Enums\CourseAccessType;
use App\Enums\CourseCompletionMode;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Support\Duration;
use App\Support\YouTube;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'category_id', 'instructor_id', 'title', 'slug', 'short_description', 'description',
    'thumbnail_path', 'banner_path', 'preview_video_url', 'preview_video_id', 'price', 'compare_at_price',
    'level', 'language', 'status', 'is_featured', 'sort_order', 'outcomes', 'requirements',
    'audience', 'certificate_enabled', 'completion_threshold', 'published_at',
    'certificate_template_id', 'access_type', 'access_duration_days', 'access_fixed_expiry_date',
    'completion_mode', 'completion_requires_live_attendance',
])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
            'level' => CourseLevel::class,
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'outcomes' => 'array',
            'requirements' => 'array',
            'audience' => 'array',
            'is_featured' => 'boolean',
            'certificate_enabled' => 'boolean',
            'published_at' => 'datetime',
            'access_type' => CourseAccessType::class,
            'access_fixed_expiry_date' => 'date',
            'completion_mode' => CourseCompletionMode::class,
            'completion_requires_live_attendance' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Instructor, $this>
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * @return HasMany<CourseModule, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<Lesson, $this>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * @return HasMany<LiveClass, $this>
     */
    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class);
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['status', 'progress_percent', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Testimonial, $this>
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * @return HasMany<Faq, $this>
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return HasMany<Certificate, $this>
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return BelongsTo<CertificateTemplate, $this>
     */
    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * @return HasMany<CourseReview, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    /**
     * @return HasMany<CourseAnnouncement, $this>
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(CourseAnnouncement::class);
    }

    /**
     * @return HasMany<CourseQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(CourseQuestion::class);
    }

    public function isPublished(): bool
    {
        return $this->status === CourseStatus::Published;
    }

    public function isFree(): bool
    {
        return (float) $this->price <= 0.0;
    }

    /**
     * The enrolment expiry date this course's access policy implies, computed
     * from the moment a student enrols. Null means access never expires.
     */
    public function computeAccessExpiry(CarbonInterface $enrolledAt): ?CarbonImmutable
    {
        return match ($this->access_type) {
            CourseAccessType::LimitedDuration => $this->access_duration_days
                ? $enrolledAt->toImmutable()->addDays($this->access_duration_days)
                : null,
            CourseAccessType::FixedExpiry => $this->access_fixed_expiry_date?->toImmutable()->endOfDay(),
            default => null,
        };
    }

    /**
     * Curriculum ordered for display, with everything the sidebar needs eager loaded.
     *
     * @return Collection<int, CourseModule>
     */
    public function curriculum(bool $publishedLessonsOnly = true): Collection
    {
        return $this->modules()
            ->with(['lessons' => function (HasMany $query) use ($publishedLessonsOnly): void {
                if ($publishedLessonsOnly) {
                    $query->where('is_published', true);
                }

                $query->with('liveClass')
                    ->withCount('resources')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            }])
            ->get();
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->banner_path
            ? Storage::disk('public')->url($this->banner_path)
            : null);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => match (true) {
            filled($this->thumbnail_path) => Storage::disk('public')->url($this->thumbnail_path),
            filled($this->preview_video_id) => YouTube::thumbnailUrl($this->preview_video_id, 'maxresdefault'),
            default => null,
        });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function previewEmbedUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => YouTube::embedUrl($this->preview_video_id));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function durationLabel(): Attribute
    {
        return Attribute::get(fn (): string => Duration::label($this->duration_minutes));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function priceLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->isFree()
            ? 'Free'
            : config('academy.currency_symbol').number_format((float) $this->price, 0));
    }

    /**
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published);
    }

    /**
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Catalogue ordering: curated position first, then newest.
     *
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }
}
