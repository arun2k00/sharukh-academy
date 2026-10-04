<?php

namespace Tests\Feature\Admin;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_links_a_trainer_account_to_an_instructor_profile(): void
    {
        $admin = $this->admin();
        $instructor = Instructor::factory()->create(['user_id' => null]);

        // A plain Trainer factory state with no instructor profile of its own
        // yet — the trainer() helper creates one automatically, which would
        // collide with the unique(user_id) constraint here.
        $trainer = User::factory()->trainer()->create();

        $response = $this->actingAs($admin)->put(route('admin.instructors.update', $instructor), [
            'name' => $instructor->name,
            'slug' => $instructor->slug,
            'user_id' => $trainer->id,
        ]);

        $response->assertRedirect(route('admin.instructors.index'));
        $this->assertSame($trainer->id, $instructor->fresh()->user_id);
    }

    public function test_an_administrator_unlinks_a_trainer_account(): void
    {
        $admin = $this->admin();
        $trainer = $this->trainer();
        $instructor = $trainer->instructor;

        $response = $this->actingAs($admin)->put(route('admin.instructors.update', $instructor), [
            'name' => $instructor->name,
            'slug' => $instructor->slug,
            'user_id' => '',
        ]);

        $response->assertRedirect(route('admin.instructors.index'));
        $this->assertNull($instructor->fresh()->user_id);
    }

    public function test_a_user_id_already_linked_to_another_instructor_is_rejected(): void
    {
        $admin = $this->admin();
        $trainer = $this->trainer();
        $otherInstructor = Instructor::factory()->create(['user_id' => null]);

        $response = $this->actingAs($admin)->put(route('admin.instructors.update', $otherInstructor), [
            'name' => $otherInstructor->name,
            'slug' => $otherInstructor->slug,
            'user_id' => $trainer->id,
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertNull($otherInstructor->fresh()->user_id);
    }
}
