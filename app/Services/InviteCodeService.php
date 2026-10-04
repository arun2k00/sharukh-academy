<?php

namespace App\Services;

use App\Models\Course;
use App\Models\InviteCode;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Trainer/admin-issued codes that grant free access to one paid course,
 * bypassing payment entirely — e.g. for scholarships, promo access, or
 * students who pay outside the platform.
 */
class InviteCodeService
{
    public function generate(Course $course, User $creator, ?int $maxUses = null, ?Carbon $expiresAt = null): InviteCode
    {
        return InviteCode::create([
            'code' => $this->uniqueCode(),
            'course_id' => $course->id,
            'created_by' => $creator->id,
            'max_uses' => $maxUses,
            'expires_at' => $expiresAt,
        ]);
    }

    public function revoke(InviteCode $inviteCode): void
    {
        // revoked_at is deliberately not fillable (see InviteCode), so a
        // service is the only thing that can ever set it.
        $inviteCode->forceFill(['revoked_at' => now()])->save();
    }

    /**
     * Redeem a code for a specific course, incrementing its use count.
     *
     * Locks the code row for the duration of the transaction so two
     * near-simultaneous redemptions can't both squeeze past a max-uses limit.
     *
     * @throws InviteCodeInvalid
     */
    public function redeem(string $code, Course $course): InviteCode
    {
        return DB::transaction(function () use ($code, $course): InviteCode {
            $inviteCode = InviteCode::query()
                ->where('code', mb_strtoupper(trim($code)))
                ->where('course_id', $course->id)
                ->lockForUpdate()
                ->first();

            if ($inviteCode === null) {
                throw InviteCodeInvalid::notFound();
            }

            if ($inviteCode->isRevoked()) {
                throw InviteCodeInvalid::revoked();
            }

            if ($inviteCode->isExpired()) {
                throw InviteCodeInvalid::expired();
            }

            if ($inviteCode->isExhausted()) {
                throw InviteCodeInvalid::exhausted();
            }

            $inviteCode->increment('used_count');

            return $inviteCode;
        });
    }

    private function uniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (InviteCode::where('code', $code)->exists());

        return $code;
    }
}
