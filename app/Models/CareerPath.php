<?php

namespace App\Models;

use Database\Factories\CareerPathFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * A curated, ordered sequence of existing courses (§ career paths) — a
 * roadmap page, not a purchasable bundle. Each attached course stays a
 * normal, independently enrollable Course; the path only groups and orders
 * them into stages for the journey diagram.
 */
#[Fillable([
    'slug', 'title', 'subtitle', 'description', 'capstone_title',
    'capstone_description', 'is_published', 'is_featured', 'sort_order',
])]
class CareerPath extends Model
{
    /** @use HasFactory<CareerPathFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot(['stage_label', 'sort_order'])
            ->withTimestamps()
            ->orderBy('career_path_course.sort_order');
    }

    /**
     * Courses grouped by stage, in path order — what the journey diagram
     * renders section by section.
     *
     * @return Collection<string, Collection<int, Course>>
     */
    public function stages(): Collection
    {
        return $this->courses->groupBy(fn (Course $course): string => $course->pivot->stage_label);
    }

    public function hasCapstone(): bool
    {
        return filled($this->capstone_title) || filled($this->capstone_description);
    }

    /**
     * @param  Builder<CareerPath>  $query
     * @return Builder<CareerPath>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * @param  Builder<CareerPath>  $query
     * @return Builder<CareerPath>
     */
    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
