<?php

namespace Tests\Feature\Tutor;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\InviteCode;
use App\Models\LessonResource;
use App\Models\LiveClass;
use App\Models\LiveClassResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The priority scenario for the Tutor dashboard: a Trainer must never be
 * able to reach another Trainer's content, including by guessing or
 * incrementing a numeric ID directly — every one of these must 403 and
 * leave the database completely unchanged.
 */
class TrainerOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_trainer_cannot_view_another_trainers_course_edit_screen(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);

        $this->actingAs($trainerA)
            ->get(route('tutor.courses.edit', $courseB))
            ->assertForbidden();
    }

    public function test_a_trainer_cannot_update_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create([
            'instructor_id' => $this->trainer()->instructor->id,
            'title' => 'Original title',
        ]);

        $this->actingAs($trainerA)
            ->put(route('tutor.courses.update', $courseB), [
                'title' => 'Hijacked title',
                'short_description' => 'x',
                'price' => 0,
                'level' => 'beginner',
                'language' => 'English',
                'status' => 'draft',
                'sort_order' => 0,
                'completion_threshold' => 100,
                'access_type' => 'lifetime',
                'completion_mode' => 'all_lessons',
            ])
            ->assertForbidden();

        $this->assertSame('Original title', $courseB->fresh()->title);
    }

    public function test_a_trainer_cannot_delete_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);

        $this->actingAs($trainerA)
            ->delete(route('tutor.courses.destroy', $courseB))
            ->assertForbidden();

        $this->assertDatabaseHas('courses', ['id' => $courseB->id, 'deleted_at' => null]);
    }

    public function test_a_trainer_cannot_publish_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->draft()->create(['instructor_id' => $this->trainer()->instructor->id]);

        $this->actingAs($trainerA)
            ->patch(route('tutor.courses.status', $courseB), ['status' => 'published'])
            ->assertForbidden();

        $this->assertTrue($courseB->fresh()->status->value === 'draft');
    }

    public function test_a_trainer_cannot_open_another_trainers_curriculum_builder(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);

        $this->actingAs($trainerA)
            ->get(route('tutor.courses.curriculum', $courseB))
            ->assertForbidden();
    }

    public function test_a_trainer_cannot_post_a_module_to_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);

        $this->actingAs($trainerA)
            ->post(route('tutor.modules.store', $courseB), ['title' => 'Smuggled module'])
            ->assertForbidden();

        $this->assertDatabaseCount('course_modules', 0);
    }

    public function test_a_trainer_cannot_edit_or_delete_a_module_belonging_to_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);
        $moduleB = CourseModule::factory()->create(['course_id' => $courseB->id, 'title' => 'Untouched']);

        $this->actingAs($trainerA)->get(route('tutor.modules.edit', $moduleB))->assertForbidden();

        $this->actingAs($trainerA)
            ->put(route('tutor.modules.update', $moduleB), ['title' => 'Hijacked'])
            ->assertForbidden();

        $this->actingAs($trainerA)
            ->delete(route('tutor.modules.destroy', $moduleB))
            ->assertForbidden();

        $this->assertSame('Untouched', $moduleB->fresh()->title);
    }

    public function test_a_trainer_cannot_reorder_a_module_belonging_to_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);
        $moduleB1 = CourseModule::factory()->create(['course_id' => $courseB->id, 'sort_order' => 1]);
        $moduleB2 = CourseModule::factory()->create(['course_id' => $courseB->id, 'sort_order' => 2]);

        $this->actingAs($trainerA)
            ->patch(route('tutor.modules.move', $moduleB2), ['direction' => 'up'])
            ->assertForbidden();

        $this->assertSame(1, $moduleB1->fresh()->sort_order);
        $this->assertSame(2, $moduleB2->fresh()->sort_order);
    }

    public function test_a_trainer_cannot_reach_a_lesson_belonging_to_another_trainers_course_by_guessing_its_id(): void
    {
        $trainerA = $this->trainer();
        $courseB = $this->courseWithCurriculum(['instructor_id' => $this->trainer()->instructor->id]);
        $lessonB = $this->firstLesson($courseB);
        $originalTitle = $lessonB->title;

        $this->actingAs($trainerA)->get(route('tutor.lessons.edit', $lessonB))->assertForbidden();

        $this->actingAs($trainerA)
            ->put(route('tutor.lessons.update', $lessonB), ['title' => 'Hijacked'])
            ->assertForbidden();

        $this->actingAs($trainerA)
            ->delete(route('tutor.lessons.destroy', $lessonB))
            ->assertForbidden();

        $this->actingAs($trainerA)
            ->patch(route('tutor.lessons.move', $lessonB), ['direction' => 'up'])
            ->assertForbidden();

        // Lessons are hard-deleted (no deleted_at column), so a plain
        // existence check plus the untouched title is the correct proof
        // that none of the denied requests above mutated anything.
        $this->assertDatabaseHas('lessons', ['id' => $lessonB->id]);
        $this->assertSame($originalTitle, $lessonB->fresh()->title);
    }

    public function test_a_trainer_cannot_post_a_lesson_into_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);
        $moduleB = CourseModule::factory()->create(['course_id' => $courseB->id]);

        $this->actingAs($trainerA)
            ->post(route('tutor.lessons.store', $courseB), [
                'course_module_id' => $moduleB->id,
                'title' => 'Smuggled lesson',
                'type' => 'video',
                'video_url' => 'https://youtu.be/M7lc1UVf-VE',
                'duration_minutes' => 5,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('lessons', 0);
    }

    public function test_a_trainer_cannot_upload_a_resource_to_another_trainers_lesson(): void
    {
        $trainerA = $this->trainer();
        $courseB = $this->courseWithCurriculum(['instructor_id' => $this->trainer()->instructor->id]);
        $lessonB = $this->firstLesson($courseB);

        $this->actingAs($trainerA)
            ->post(route('tutor.lessons.resources.store', $lessonB), [
                'title' => 'Smuggled file',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('lesson_resources', 0);
    }

    public function test_a_trainer_cannot_delete_a_resource_belonging_to_another_trainers_lesson(): void
    {
        $trainerA = $this->trainer();
        $courseB = $this->courseWithCurriculum(['instructor_id' => $this->trainer()->instructor->id]);
        $lessonB = $this->firstLesson($courseB);
        $resourceB = LessonResource::factory()->create(['lesson_id' => $lessonB->id]);

        $this->actingAs($trainerA)
            ->delete(route('tutor.lessons.resources.destroy', $resourceB))
            ->assertForbidden();

        $this->assertDatabaseHas('lesson_resources', ['id' => $resourceB->id]);
    }

    public function test_a_trainer_cannot_edit_or_delete_a_live_class_belonging_to_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $instructorB = $this->trainer()->instructor;
        $courseB = Course::factory()->create(['instructor_id' => $instructorB->id]);
        $liveClassB = LiveClass::factory()->create([
            'course_id' => $courseB->id,
            'instructor_id' => $instructorB->id,
            'title' => 'Untouched session',
        ]);

        $this->actingAs($trainerA)->get(route('tutor.live-classes.edit', $liveClassB))->assertForbidden();

        $this->actingAs($trainerA)
            ->put(route('tutor.live-classes.update', $liveClassB), ['title' => 'Hijacked session'])
            ->assertForbidden();

        $this->actingAs($trainerA)
            ->delete(route('tutor.live-classes.destroy', $liveClassB))
            ->assertForbidden();

        $this->assertSame('Untouched session', $liveClassB->fresh()->title);
    }

    public function test_a_trainer_cannot_upload_a_resource_to_another_trainers_live_class(): void
    {
        $trainerA = $this->trainer();
        $instructorB = $this->trainer()->instructor;
        $courseB = Course::factory()->create(['instructor_id' => $instructorB->id]);
        $liveClassB = LiveClass::factory()->create(['course_id' => $courseB->id, 'instructor_id' => $instructorB->id]);

        $this->actingAs($trainerA)
            ->post(route('tutor.live-classes.resources.store', $liveClassB), ['title' => 'Smuggled file'])
            ->assertForbidden();

        $this->assertDatabaseCount('live_class_resources', 0);
    }

    public function test_a_trainer_cannot_delete_a_resource_belonging_to_another_trainers_live_class(): void
    {
        $trainerA = $this->trainer();
        $instructorB = $this->trainer()->instructor;
        $courseB = Course::factory()->create(['instructor_id' => $instructorB->id]);
        $liveClassB = LiveClass::factory()->create(['course_id' => $courseB->id, 'instructor_id' => $instructorB->id]);
        $resourceB = LiveClassResource::factory()->create(['live_class_id' => $liveClassB->id]);

        $this->actingAs($trainerA)
            ->delete(route('tutor.live-classes.resources.destroy', $resourceB))
            ->assertForbidden();

        $this->assertDatabaseHas('live_class_resources', ['id' => $resourceB->id]);
    }

    public function test_a_trainer_cannot_view_or_generate_invite_codes_for_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $courseB = Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id]);

        $this->actingAs($trainerA)
            ->get(route('tutor.courses.invite-codes.index', $courseB))
            ->assertForbidden();

        $this->actingAs($trainerA)
            ->post(route('tutor.courses.invite-codes.store', $courseB), [])
            ->assertForbidden();

        $this->assertDatabaseCount('invite_codes', 0);
    }

    public function test_a_trainer_cannot_revoke_an_invite_code_belonging_to_another_trainers_course(): void
    {
        $trainerA = $this->trainer();
        $instructorB = $this->trainer()->instructor;
        $courseB = Course::factory()->create(['instructor_id' => $instructorB->id]);
        $codeB = InviteCode::factory()->create(['course_id' => $courseB->id]);

        $this->actingAs($trainerA)
            ->delete(route('tutor.invite-codes.destroy', $codeB))
            ->assertForbidden();

        $this->assertNull($codeB->fresh()->revoked_at);
    }

    public function test_a_trainer_with_no_courses_sees_an_empty_list_never_another_trainers(): void
    {
        $trainerA = $this->trainer();
        Course::factory()->create(['instructor_id' => $this->trainer()->instructor->id, 'title' => 'Not mine']);

        $this->actingAs($trainerA)
            ->get(route('tutor.courses.index'))
            ->assertOk()
            ->assertDontSee('Not mine');
    }
}
