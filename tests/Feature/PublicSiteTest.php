<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Faq;
use App\Models\Instructor;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_homepage_renders_with_no_content_at_all(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Courses are on their way');
    }

    public function test_the_homepage_shows_featured_courses(): void
    {
        $featured = Course::factory()->featured()->create();

        $this->get(route('home'))->assertOk()->assertSee($featured->title);
    }

    public function test_the_homepage_shows_testimonials_and_faqs_but_not_instructors(): void
    {
        $instructor = Instructor::factory()->featured()->create();
        $testimonial = Testimonial::factory()->featured()->create();
        Faq::factory()->create(['question' => 'Do you offer refunds?']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($testimonial->name)
            ->assertSee('Do you offer refunds?')
            ->assertDontSee($instructor->name);
    }

    public function test_the_faculty_page_shows_every_instructor_with_the_featured_one_leading(): void
    {
        $lead = Instructor::factory()->featured()->create(['name' => 'Priyanka Lead']);
        $other = Instructor::factory()->create(['name' => 'Rahul Support']);

        $this->get(route('faculty'))
            ->assertOk()
            ->assertSeeInOrder([$lead->name, $other->name]);
    }

    public function test_the_catalogue_lists_published_courses_and_filters_by_category(): void
    {
        $first = Course::factory()->create();
        $second = Course::factory()->create();

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee($first->title)
            ->assertSee($second->title);

        $first->loadMissing('category');

        // Assert on the collection the view received: the footer legitimately
        // links every published course, so scanning the whole page would lie.
        $filtered = $this->get(route('courses.index', ['category' => $first->category->slug]))
            ->assertOk()
            ->viewData('courses');

        $this->assertTrue($filtered->contains('id', $first->id));
        $this->assertFalse($filtered->contains('id', $second->id));
    }

    public function test_the_course_page_shows_the_curriculum(): void
    {
        $course = $this->courseWithCurriculum();
        $lesson = $this->firstLesson($course);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSee($lesson->title);
    }

    public function test_the_about_and_contact_pages_render(): void
    {
        $this->get(route('about'))->assertOk();
        $this->get(route('contact'))->assertOk();
    }

    public function test_the_contact_form_stores_a_message(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Vikram Shah',
            'email' => 'vikram@example.test',
            'subject' => 'Corporate enrolment',
            'message' => 'We would like to enrol six people from our marketing team next quarter.',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'vikram@example.test',
            'is_read' => false,
        ]);
    }

    public function test_the_contact_form_rejects_an_empty_submission(): void
    {
        $this->post(route('contact.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_the_contact_form_is_rate_limited(): void
    {
        $payload = [
            'name' => 'Repeat Sender',
            'email' => 'repeat@example.test',
            'subject' => 'Hello',
            'message' => 'This message is long enough to satisfy the validation rules.',
        ];

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('contact.store'), $payload)->assertRedirect();
        }

        $this->post(route('contact.store'), $payload)->assertStatus(429);

        $this->assertSame(3, ContactMessage::count());
    }

    public function test_an_unknown_page_renders_the_friendly_not_found_screen(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee('We could not find that page');
    }

    public function test_course_search_filters_by_title(): void
    {
        $match = Course::factory()->create(['title' => 'Advanced Copywriting Workshop']);
        $other = Course::factory()->create(['title' => 'Financial Modelling Basics']);

        $filtered = $this->get(route('courses.index', ['q' => 'copywriting']))
            ->assertOk()
            ->viewData('courses');

        $this->assertTrue($filtered->contains('id', $match->id));
        $this->assertFalse($filtered->contains('id', $other->id));
    }

    public function test_approved_reviews_are_shown_on_the_course_page_but_hidden_ones_are_not(): void
    {
        $course = $this->courseWithCurriculum();

        $visible = CourseReview::factory()->create([
            'course_id' => $course->id,
            'review' => 'Genuinely excellent, changed how I plan a quarter.',
            'is_approved' => true,
        ]);
        $hidden = CourseReview::factory()->create([
            'course_id' => $course->id,
            'review' => 'This review was hidden by moderation.',
            'is_approved' => false,
        ]);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Genuinely excellent, changed how I plan a quarter.')
            ->assertDontSee('This review was hidden by moderation.');
    }
}
