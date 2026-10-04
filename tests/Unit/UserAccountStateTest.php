<?php

namespace Tests\Unit;

use App\Enums\AccountType;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_trainer_reflects_the_role_column(): void
    {
        $this->assertTrue($this->trainer()->isTrainer());
        $this->assertFalse($this->student()->isTrainer());
        $this->assertFalse($this->admin()->isTrainer());
    }

    public function test_is_pro_reflects_the_account_type_column(): void
    {
        $free = $this->student();
        $pro = User::factory()->create(['account_type' => AccountType::Pro]);

        $this->assertFalse($free->isPro());
        $this->assertTrue($pro->isPro());
    }

    public function test_is_suspended_reflects_the_suspended_at_column(): void
    {
        $active = $this->student();
        $suspended = User::factory()->create(['suspended_at' => now()]);

        $this->assertFalse($active->isSuspended());
        $this->assertTrue($suspended->isSuspended());
    }

    public function test_a_user_and_an_instructor_profile_link_both_ways(): void
    {
        $trainer = $this->trainer();

        $this->assertInstanceOf(Instructor::class, $trainer->instructor);
        $this->assertTrue($trainer->instructor->user->is($trainer));
    }

    public function test_a_user_with_no_linked_instructor_profile_has_none(): void
    {
        $student = $this->student();

        $this->assertNull($student->instructor);
    }

    public function test_the_trainers_scope_returns_only_trainer_accounts(): void
    {
        $trainer = $this->trainer();
        $this->student();
        $this->admin();

        $trainers = User::query()->trainers()->get();

        $this->assertCount(1, $trainers);
        $this->assertTrue($trainers->first()->is($trainer));
    }

    public function test_the_suspended_scope_returns_only_suspended_accounts(): void
    {
        $suspended = User::factory()->create(['suspended_at' => now()]);
        $this->student();

        $results = User::query()->suspended()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($suspended));
    }

    public function test_a_course_is_owned_by_the_trainer_it_is_attributed_to(): void
    {
        $trainer = $this->trainer();
        $otherTrainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);

        $this->assertTrue($course->isOwnedBy($trainer));
        $this->assertFalse($course->isOwnedBy($otherTrainer));
        $this->assertFalse($course->isOwnedBy($this->student()));
    }

    public function test_a_course_with_no_instructor_is_owned_by_nobody(): void
    {
        $course = Course::factory()->create(['instructor_id' => null]);

        $this->assertFalse($course->isOwnedBy($this->trainer()));
    }
}
