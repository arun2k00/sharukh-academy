<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_creates_a_student_account(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.students.store'), [
                'name' => 'Kwame Boateng',
                'email' => 'kwame@example.test',
                'password' => 'a-strong-password',
                'password_confirmation' => 'a-strong-password',
                'role' => 'student',
                'email_verified' => '1',
            ])
            ->assertRedirect();

        $student = User::where('email', 'kwame@example.test')->firstOrFail();

        $this->assertSame(UserRole::Student, $student->role);
        $this->assertTrue($student->hasVerifiedEmail());
    }

    public function test_the_student_list_never_exposes_password_hashes(): void
    {
        $student = User::factory()->create(['password' => Hash::make('a-secret')]);

        $this->actingAs($this->admin())
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertDontSee($student->getAuthPassword());

        $this->actingAs($this->admin())
            ->get(route('admin.students.show', $student))
            ->assertOk()
            ->assertDontSee($student->getAuthPassword());
    }

    public function test_updating_without_a_password_keeps_the_existing_one(): void
    {
        $student = User::factory()->create(['password' => 'the-original-password']);
        $originalHash = $student->getAuthPassword();

        $this->actingAs($this->admin())
            ->put(route('admin.students.update', $student), [
                'name' => 'Renamed Student',
                'email' => $student->email,
                'role' => 'student',
            ])
            ->assertRedirect();

        $student->refresh();

        $this->assertSame('Renamed Student', $student->name);
        $this->assertSame($originalHash, $student->getAuthPassword());
    }

    public function test_an_administrator_cannot_remove_their_own_administrator_access(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.students.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'student',
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_an_administrator_cannot_delete_their_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.students.destroy', $admin))
            ->assertForbidden();

        $this->assertNotSoftDeleted('users', ['id' => $admin->id]);
    }

    public function test_one_administrator_cannot_delete_another(): void
    {
        $other = $this->admin();

        $this->actingAs($this->admin())
            ->delete(route('admin.students.destroy', $other))
            ->assertForbidden();
    }

    public function test_a_student_account_can_be_removed(): void
    {
        $student = $this->student();

        $this->actingAs($this->admin())
            ->delete(route('admin.students.destroy', $student))
            ->assertRedirect(route('admin.students.index'));

        $this->assertSoftDeleted('users', ['id' => $student->id]);
    }

    public function test_a_student_cannot_open_another_students_admin_profile(): void
    {
        $other = $this->student();

        $this->actingAs($this->student())
            ->get(route('admin.students.show', $other))
            ->assertForbidden();
    }
}
