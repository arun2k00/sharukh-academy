<?php

namespace Tests\Unit\Services;

use App\Models\Course;
use App\Services\InviteCodeInvalid;
use App\Services\InviteCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    private InviteCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(InviteCodeService::class);
    }

    public function test_generate_creates_a_unique_uppercase_code(): void
    {
        $course = Course::factory()->create();
        $trainer = $this->trainer();

        $code = $this->service->generate($course, $trainer);

        $this->assertSame($course->id, $code->course_id);
        $this->assertSame($trainer->id, $code->created_by);
        $this->assertSame(mb_strtoupper($code->code), $code->code);
        $this->assertNull($code->max_uses);
        $this->assertNull($code->expires_at);
    }

    public function test_redeeming_a_valid_code_increments_its_use_count(): void
    {
        $course = Course::factory()->create();
        $code = $this->service->generate($course, $this->trainer());

        $redeemed = $this->service->redeem($code->code, $course);

        $this->assertSame(1, $redeemed->fresh()->used_count);
    }

    public function test_redeeming_is_case_insensitive(): void
    {
        $course = Course::factory()->create();
        $code = $this->service->generate($course, $this->trainer());

        $redeemed = $this->service->redeem(mb_strtolower($code->code), $course);

        $this->assertSame($code->id, $redeemed->id);
    }

    public function test_an_unknown_code_is_rejected(): void
    {
        $course = Course::factory()->create();

        $this->expectException(InviteCodeInvalid::class);

        $this->service->redeem('NOPE', $course);
    }

    public function test_a_code_cannot_be_redeemed_against_a_different_course(): void
    {
        $courseA = Course::factory()->create();
        $courseB = Course::factory()->create();
        $code = $this->service->generate($courseA, $this->trainer());

        $this->expectException(InviteCodeInvalid::class);

        $this->service->redeem($code->code, $courseB);
    }

    public function test_a_revoked_code_is_rejected(): void
    {
        $course = Course::factory()->create();
        $code = $this->service->generate($course, $this->trainer());
        $this->service->revoke($code);

        $this->expectException(InviteCodeInvalid::class);

        $this->service->redeem($code->code, $course);
    }

    public function test_an_expired_code_is_rejected(): void
    {
        $course = Course::factory()->create();
        $code = $this->service->generate($course, $this->trainer(), expiresAt: now()->subDay());

        $this->expectException(InviteCodeInvalid::class);

        $this->service->redeem($code->code, $course);
    }

    public function test_a_code_is_rejected_once_it_reaches_its_max_uses(): void
    {
        $course = Course::factory()->create();
        $code = $this->service->generate($course, $this->trainer(), maxUses: 1);

        $this->service->redeem($code->code, $course);

        $this->expectException(InviteCodeInvalid::class);

        $this->service->redeem($code->code, $course);
    }

    public function test_a_code_with_a_max_uses_is_still_usable_below_the_limit(): void
    {
        $course = Course::factory()->create();
        $code = $this->service->generate($course, $this->trainer(), maxUses: 2);

        $this->service->redeem($code->code, $course);
        $redeemed = $this->service->redeem($code->code, $course);

        $this->assertSame(2, $redeemed->fresh()->used_count);
    }
}
