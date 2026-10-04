<?php

namespace Tests\Feature\Admin;

use App\Models\CareerPath;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerPathManagementTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Data Engineering — Complete Job-Ready Program',
            'subtitle' => 'SQL, Python, Azure Data Factory, Snowflake, dbt and Power BI, in order.',
            'description' => 'A staged sequence of existing courses, from foundations to a real-world project.',
            'is_published' => '1',
            'sort_order' => 0,
            'courses' => [],
        ], $overrides);
    }

    public function test_a_student_may_not_reach_career_path_administration(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.career-paths.index'))
            ->assertForbidden();
    }

    public function test_the_admin_can_create_a_career_path_with_staged_courses(): void
    {
        $sql = Course::factory()->create();
        $python = Course::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.career-paths.store'), $this->validPayload([
                'courses' => [
                    ['course_id' => $sql->id, 'stage_label' => 'FOUNDATION', 'sort_order' => 1],
                    ['course_id' => $python->id, 'stage_label' => 'FOUNDATION', 'sort_order' => 2],
                ],
            ]))
            ->assertRedirect(route('admin.career-paths.index'));

        $path = CareerPath::firstOrFail();

        $this->assertSame('data-engineering-complete-job-ready-program', $path->slug);
        $this->assertSame([$sql->id, $python->id], $path->courses()->pluck('courses.id')->all());
        $this->assertSame('FOUNDATION', $path->courses()->first()->pivot->stage_label);
    }

    public function test_the_admin_can_upload_a_banner_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.career-paths.store'), $this->validPayload([
                'banner' => UploadedFile::fake()->image('banner.jpg', 1600, 500),
            ]))
            ->assertRedirect(route('admin.career-paths.index'));

        $path = CareerPath::firstOrFail();

        $this->assertNotNull($path->banner_path);
        Storage::disk('public')->assertExists($path->banner_path);
        $this->assertNotNull($path->banner_url);
    }

    public function test_updating_a_career_path_replaces_its_course_list(): void
    {
        $path = CareerPath::factory()->create();
        $original = Course::factory()->create();
        $path->courses()->attach($original->id, ['stage_label' => 'FOUNDATION', 'sort_order' => 1]);

        $replacement = Course::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('admin.career-paths.update', $path), $this->validPayload([
                'courses' => [
                    ['course_id' => $replacement->id, 'stage_label' => 'ANALYTICS', 'sort_order' => 1],
                ],
            ]))
            ->assertRedirect(route('admin.career-paths.index'));

        $path->refresh();

        $this->assertSame([$replacement->id], $path->courses->pluck('id')->all());
        $this->assertSame('ANALYTICS', $path->courses->first()->pivot->stage_label);
    }

    public function test_the_same_course_cannot_be_attached_twice_in_one_submission(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.career-paths.store'), $this->validPayload([
                'courses' => [
                    ['course_id' => $course->id, 'stage_label' => 'FOUNDATION', 'sort_order' => 1],
                    ['course_id' => $course->id, 'stage_label' => 'ANALYTICS', 'sort_order' => 2],
                ],
            ]))
            ->assertSessionHasErrors(['courses.0.course_id', 'courses.1.course_id']);

        $this->assertDatabaseCount('career_paths', 0);
    }

    public function test_deleting_a_career_path_does_not_delete_its_courses(): void
    {
        $path = CareerPath::factory()->create();
        $course = Course::factory()->create();
        $path->courses()->attach($course->id, ['stage_label' => 'FOUNDATION', 'sort_order' => 1]);

        $this->actingAs($this->admin())
            ->delete(route('admin.career-paths.destroy', $path))
            ->assertRedirect(route('admin.career-paths.index'));

        $this->assertDatabaseMissing('career_paths', ['id' => $path->id]);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }
}
