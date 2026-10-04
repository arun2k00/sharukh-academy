<?php

namespace Tests\Feature\Tutor;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Performance Marketing Foundations',
            'short_description' => 'Plan and defend a paid acquisition programme.',
            'description' => "First paragraph.\n\nSecond paragraph.",
            'category_id' => Category::factory()->create()->id,
            'price' => 12900,
            'level' => 'beginner',
            'language' => 'English',
            'status' => 'draft',
            'sort_order' => 1,
            'outcomes' => "Size a market\nSet a budget",
            'requirements' => 'A spreadsheet',
            'audience' => 'Marketers',
            'completion_threshold' => 100,
            'access_type' => 'lifetime',
            'completion_mode' => 'all_lessons',
        ], $overrides);
    }

    public function test_a_trainer_creates_a_course_owned_by_their_own_instructor_profile(): void
    {
        $trainer = $this->trainer();

        $this->actingAs($trainer)
            ->post(route('tutor.courses.store'), $this->validPayload())
            ->assertRedirect();

        $course = Course::firstOrFail();

        $this->assertSame('performance-marketing-foundations', $course->slug);
        $this->assertSame($trainer->instructor->id, $course->instructor_id);
    }

    public function test_the_form_has_no_instructor_field_to_pick_someone_else(): void
    {
        $trainer = $this->trainer();
        $someoneElsesInstructor = Instructor::factory()->create();

        // Even if an instructor_id were somehow submitted, it must be ignored —
        // the request rules don't define the field at all, so it never reaches
        // mass assignment.
        $this->actingAs($trainer)
            ->post(route('tutor.courses.store'), $this->validPayload([
                'instructor_id' => $someoneElsesInstructor->id,
            ]))
            ->assertRedirect();

        $course = Course::firstOrFail();

        $this->assertSame($trainer->instructor->id, $course->instructor_id);
        $this->assertNotSame($someoneElsesInstructor->id, $course->instructor_id);
    }

    public function test_publishing_a_course_stamps_the_publication_date(): void
    {
        $trainer = $this->trainer();

        $this->actingAs($trainer)
            ->post(route('tutor.courses.store'), $this->validPayload(['status' => 'published']));

        $this->assertNotNull(Course::firstOrFail()->published_at);
    }

    public function test_a_trainer_updates_their_own_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);

        $this->actingAs($trainer)
            ->put(route('tutor.courses.update', $course), $this->validPayload([
                'title' => 'A renamed course',
                'slug' => $course->slug,
            ]))
            ->assertRedirect(route('tutor.courses.edit', $course));

        $this->assertSame('A renamed course', $course->fresh()->title);
    }

    public function test_a_trainer_self_publishes_via_the_status_shortcut(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->draft()->create(['instructor_id' => $trainer->instructor->id]);

        $this->actingAs($trainer)
            ->patch(route('tutor.courses.status', $course), ['status' => 'published'])
            ->assertRedirect();

        $this->assertSame(CourseStatus::Published, $course->fresh()->status);
    }

    public function test_deleting_own_course_soft_deletes_it_and_keeps_curriculum(): void
    {
        $trainer = $this->trainer();
        $course = $this->courseWithCurriculum(['instructor_id' => $trainer->instructor->id]);

        $this->actingAs($trainer)
            ->delete(route('tutor.courses.destroy', $course))
            ->assertRedirect(route('tutor.courses.index'));

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        $this->assertDatabaseCount('lessons', 4);
    }

    public function test_a_trainer_cannot_restore_a_deleted_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->create(['instructor_id' => $trainer->instructor->id]);
        $course->delete();

        $this->actingAs($trainer)
            ->patch(route('admin.courses.restore', $course))
            ->assertForbidden();
    }

    public function test_a_trainer_can_preview_their_own_draft_course(): void
    {
        $trainer = $this->trainer();
        $course = Course::factory()->draft()->create(['instructor_id' => $trainer->instructor->id]);

        $this->actingAs($trainer)
            ->get(route('courses.show', $course))
            ->assertOk();
    }

    public function test_the_course_index_renders_a_category_without_lazy_loading_it(): void
    {
        $trainer = $this->trainer();
        $category = Category::factory()->create(['name' => 'Data Engineering']);
        Course::factory()->create([
            'instructor_id' => $trainer->instructor->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($trainer)
            ->get(route('tutor.courses.index'))
            ->assertOk()
            ->assertSee('Data Engineering');
    }
}
