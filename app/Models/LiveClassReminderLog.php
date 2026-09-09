<?php

namespace App\Models;

use App\Enums\LiveClassReminderType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pure bookkeeping: guarantees the reminder command sends each notification
 * type at most once per class, so re-running the schedule is always safe.
 */
#[Fillable(['live_class_id', 'type', 'sent_at'])]
class LiveClassReminderLog extends Model
{
    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LiveClassReminderType::class,
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<LiveClass, $this>
     */
    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }
}
