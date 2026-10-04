<?php

namespace Tests\Feature;

use App\Models\CareerPath;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerPathTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_index_page_lists_published_paths_only(): void
    {
        $published = CareerPath::factory()->create(['is_published' => true]);
        $draft = CareerPath::factory()->create(['is_published' => false]);

        $this->get(route('career-paths.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_the_show_page_groups_courses_by_stage_in_order(): void
    {
        $sql = Course::factory()->create(['title' => 'SQL for Data Engineering']);
        $python = Course::factory()->create(['title' => 'Python for Data Engineering']);
        $snowflake = Course::factory()->create(['title' => 'Snowflake Masterclass']);

        $path = CareerPath::factory()->create();
        $path->courses()->attach([
            $sql->id => ['stage_label' => 'FOUNDATION', 'sort_order' => 1],
            $python->id => ['stage_label' => 'FOUNDATION', 'sort_order' => 2],
            $snowflake->id => ['stage_label' => 'DATA ENGINEERING', 'sort_order' => 3],
        ]);

        $stages = $path->fresh()->stages();

        $this->assertSame(['FOUNDATION', 'DATA ENGINEERING'], $stages->keys()->all());
        $this->assertSame([$sql->id, $python->id], $stages['FOUNDATION']->pluck('id')->all());

        $this->get(route('career-paths.show', $path))
            ->assertOk()
            ->assertSeeInOrder([$sql->title, $python->title, $snowflake->title]);
    }

    public function test_a_draft_path_is_hidden_from_guests_but_visible_to_an_admin(): void
    {
        $draft = CareerPath::factory()->create(['is_published' => false]);

        $this->get(route('career-paths.show', $draft))->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('career-paths.show', $draft))
            ->assertOk();
    }

    public function test_the_capstone_only_renders_when_set(): void
    {
        $withCapstone = CareerPath::factory()->create([
            'capstone_title' => 'Build a production data pipeline',
            'capstone_description' => 'Combine every tool above into one end-to-end project.',
        ]);
        $withoutCapstone = CareerPath::factory()->create([
            'capstone_title' => null,
            'capstone_description' => null,
        ]);

        $this->get(route('career-paths.show', $withCapstone))
            ->assertOk()
            ->assertSee('Build a production data pipeline');

        $this->get(route('career-paths.show', $withoutCapstone))
            ->assertOk()
            ->assertDontSee('Real-world project');
    }

    public function test_the_show_page_lists_the_paths_instructors_and_course_stats(): void
    {
        $instructor = Instructor::factory()->create(['name' => 'Sharuk Shaik']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id, 'duration_minutes' => 120]);
        $path = CareerPath::factory()->create();
        $path->courses()->attach($course->id, ['stage_label' => 'FOUNDATION', 'sort_order' => 1]);

        $this->get(route('career-paths.show', $path))
            ->assertOk()
            ->assertSee('Sharuk Shaik')
            ->assertSee('Hours of instruction')
            ->assertSee('2'); // 120 minutes = 2 hours
    }

    public function test_an_enrolled_students_course_shows_continue_instead_of_view_course(): void
    {
        $course = Course::factory()->create();
        $path = CareerPath::factory()->create();
        $path->courses()->attach($course->id, ['stage_label' => 'FOUNDATION', 'sort_order' => 1]);

        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->get(route('career-paths.show', $path))
            ->assertOk()
            ->assertSee('Continue');
    }

    public function test_a_course_page_links_back_to_the_career_paths_it_belongs_to(): void
    {
        $course = $this->courseWithCurriculum();
        $path = CareerPath::factory()->create(['title' => 'Data Engineering — Complete Job-Ready Program']);
        $path->courses()->attach($course->id, ['stage_label' => 'FOUNDATION', 'sort_order' => 1]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Data Engineering — Complete Job-Ready Program');
    }
}
