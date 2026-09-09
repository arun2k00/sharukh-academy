<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Records who did what in the admin panel (§19).
 *
 * Deliberately narrow: an action key, a human description, and an optional
 * subject. Never given request payloads or model attributes directly, so
 * there is no route by which sensitive field values end up in the log.
 */
class AdminActivityLogger
{
    public function log(string $action, ?Model $subject, string $description): void
    {
        AdminActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => Str::limit($description, 500, ''),
            'created_at' => now(),
        ]);
    }
}
