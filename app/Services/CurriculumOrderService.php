<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Keeps `sort_order` values contiguous and moves an item one place at a time.
 *
 * Reordering rewrites the whole sibling set inside a transaction, so a list
 * that was previously inconsistent (duplicate or sparse positions) is repaired
 * rather than perpetuated.
 */
class CurriculumOrderService
{
    public function moveModule(CourseModule $module, string $direction): void
    {
        $siblings = CourseModule::query()
            ->where('course_id', $module->course_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->reorder($siblings, $module, $direction);
    }

    public function moveLesson(Lesson $lesson, string $direction): void
    {
        $siblings = Lesson::query()
            ->where('course_module_id', $lesson->course_module_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->reorder($siblings, $lesson, $direction);
    }

    /**
     * Position a newly created record at the end of its list.
     */
    public function nextPositionForModule(int $courseId): int
    {
        return (int) CourseModule::query()->where('course_id', $courseId)->max('sort_order') + 1;
    }

    public function nextPositionForLesson(int $moduleId): int
    {
        return (int) Lesson::query()->where('course_module_id', $moduleId)->max('sort_order') + 1;
    }

    /**
     * @param  Collection<int, Model>  $siblings
     */
    private function reorder(Collection $siblings, Model $subject, string $direction): void
    {
        $index = $siblings->search(fn (Model $item): bool => $item->getKey() === $subject->getKey());

        if ($index === false) {
            return;
        }

        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $siblings->count()) {
            return;
        }

        $ordered = $siblings->values()->all();
        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        DB::transaction(function () use ($ordered): void {
            foreach ($ordered as $position => $item) {
                $item->forceFill(['sort_order' => $position + 1])->save();
            }
        });
    }
}
