<?php

namespace Tests\Feature\Admin;

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
            'instructor_id' => Instructor::factory()->create()->id,
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

    public function test_an_administrator_creates_a_course(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.courses.store'), $this->validPayload())
            ->assertRedirect();

        $course = Course::firstOrFail();

        $this->assertSame('performance-marketing-foundations', $course->slug);
        $this->assertSame(CourseStatus::Draft, $course->status);
        $this->assertSame(['Size a market', 'Set a budget'], $course->outcomes);
    }

    public function test_publishing_a_course_stamps_the_publication_date(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.courses.store'), $this->validPayload(['status' => 'published']));

        $this->assertNotNull(Course::firstOrFail()->published_at);
    }

    public function test_a_course_slug_must_be_unique(): void
    {
        Course::factory()->create(['slug' => 'performance-marketing-foundations']);

        $this->actingAs($this->admin())
            ->post(route('admin.courses.store'), $this->validPayload())
            ->assertSessionHasErrors('slug');
    }

    public function test_creating_a_course_requires_a_title_and_description(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.courses.store'), ['price' => 0])
            ->assertSessionHasErrors(['title', 'short_description', 'level', 'status']);
    }

    public function test_a_malformed_preview_video_link_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.courses.store'), $this->validPayload([
                'preview_video_url' => 'https://vimeo.com/123456',
            ]))
            ->assertSessionHasErrors('preview_video_url');
    }

    public function test_only_the_extracted_video_id_is_stored(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.courses.store'), $this->validPayload([
                'preview_video_url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE&t=42s',
            ]));

        $this->assertSame('M7lc1UVf-VE', Course::firstOrFail()->preview_video_id);
    }

    public function test_a_course_can_be_updated(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.courses.update', $course), $this->validPayload([
                'title' => 'A renamed course',
                'slug' => $course->slug,
            ]))
            ->assertRedirect(route('admin.courses.edit', $course));

        $this->assertSame('A renamed course', $course->fresh()->title);
    }

    public function test_the_status_shortcut_publishes_a_course(): void
    {
        $course = Course::factory()->draft()->create();

        $this->actingAs($this->admin())
            ->patch(route('admin.courses.status', $course), ['status' => 'published'])
            ->assertRedirect();

        $this->assertSame(CourseStatus::Published, $course->fresh()->status);
    }

    public function test_deleting_a_course_soft_deletes_it_and_keeps_student_records(): void
    {
        $course = $this->courseWithCurriculum();

        $this->actingAs($this->admin())
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'));

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        $this->assertDatabaseCount('lessons', 4);
    }

    public function test_a_deleted_course_leaves_the_public_catalogue(): void
    {
        $course = $this->courseWithCurriculum();
        $course->delete();

        $this->get(route('courses.index'))->assertOk()->assertDontSee($course->title);
        $this->get(route('courses.show', $course->slug))->assertNotFound();
    }

    public function test_a_deleted_course_can_be_found_and_restored(): void
    {
        $course = $this->courseWithCurriculum();
        $course->delete();

        // It is hidden from the default list but visible under the Deleted filter.
        $this->actingAs($this->admin())
            ->get(route('admin.courses.index'))
            ->assertOk()
            ->assertDontSee($course->title);

        $this->actingAs($this->admin())
            ->get(route('admin.courses.index', ['status' => 'deleted']))
            ->assertOk()
            ->assertSee($course->title);

        $this->actingAs($this->admin())
            ->patch(route('admin.courses.restore', $course))
            ->assertRedirect(route('admin.courses.edit', $course));

        $this->assertNotSoftDeleted('courses', ['id' => $course->id]);
        $this->assertDatabaseCount('lessons', 4);
    }

    public function test_a_student_cannot_restore_a_deleted_course(): void
    {
        $course = $this->courseWithCurriculum();
        $course->delete();

        $this->actingAs($this->student())
            ->patch(route('admin.courses.restore', $course))
            ->assertForbidden();

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }
}
