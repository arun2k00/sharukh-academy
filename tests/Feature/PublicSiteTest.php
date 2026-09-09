<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Course;
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

    public function test_the_homepage_shows_the_featured_instructor_and_testimonials(): void
    {
        $instructor = Instructor::factory()->featured()->create();
        $testimonial = Testimonial::factory()->featured()->create();
        Faq::factory()->create(['question' => 'Do you offer refunds?']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($instructor->name)
            ->assertSee($testimonial->name)
            ->assertSee('Do you offer refunds?');
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
}
