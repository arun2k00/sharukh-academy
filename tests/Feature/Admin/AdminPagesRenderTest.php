<?php

namespace Tests\Feature\Admin;

use App\Models\CareerPath;
use App\Models\Category;
use App\Models\CertificateTemplate;
use App\Models\ContactMessage;
use App\Models\CourseAnnouncement;
use App\Models\CourseModule;
use App\Models\CourseQuestion;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\Instructor;
use App\Models\LiveClass;
use App\Models\SupportTicket;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Every admin screen is rendered against real records.
 *
 * A missing variable or a mistyped route in a Blade template only shows up when
 * the template is actually compiled, so each one is exercised here.
 */
class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_screen_renders(): void
    {
        $admin = $this->admin();
        $course = $this->courseWithCurriculum();
        $module = CourseModule::where('course_id', $course->id)->firstOrFail();
        $lesson = $this->firstLesson($course);
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);
        $category = Category::factory()->create();
        $instructor = Instructor::factory()->create();
        $testimonial = Testimonial::factory()->create();
        $faq = Faq::factory()->create();
        $message = ContactMessage::factory()->create();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $certificateTemplate = CertificateTemplate::factory()->create();
        $careerPath = CareerPath::factory()->create();
        $careerPath->courses()->attach($course->id, ['stage_label' => 'FOUNDATION', 'sort_order' => 1]);
        $announcement = CourseAnnouncement::factory()->create(['course_id' => $course->id]);
        $review = CourseReview::factory()->create(['course_id' => $course->id, 'user_id' => $student->id]);
        $question = CourseQuestion::factory()->create(['course_id' => $course->id, 'user_id' => $student->id]);
        $ticket = SupportTicket::factory()->create(['user_id' => $student->id]);

        $urls = [
            route('admin.dashboard'),
            route('admin.courses.index'),
            route('admin.courses.create'),
            route('admin.courses.edit', $course),
            route('admin.courses.curriculum', $course),
            route('admin.modules.edit', $module),
            route('admin.lessons.create', $course),
            route('admin.lessons.create', ['course' => $course, 'module' => $module->id]),
            route('admin.lessons.edit', $lesson),
            route('admin.live-classes.index'),
            route('admin.live-classes.index', ['scope' => 'past']),
            route('admin.live-classes.create'),
            route('admin.live-classes.create', ['course' => $course->id]),
            route('admin.live-classes.edit', $liveClass),
            route('admin.students.index'),
            route('admin.students.index', ['q' => 'a', 'role' => 'student']),
            route('admin.students.create'),
            route('admin.students.show', $student),
            route('admin.students.edit', $student),
            route('admin.enrollments.index'),
            route('admin.enrollments.create'),
            route('admin.certificates.index'),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.categories.edit', $category),
            route('admin.career-paths.index'),
            route('admin.career-paths.create'),
            route('admin.career-paths.edit', $careerPath),
            route('admin.instructors.index'),
            route('admin.instructors.create'),
            route('admin.instructors.edit', $instructor),
            route('admin.testimonials.index'),
            route('admin.testimonials.create'),
            route('admin.testimonials.edit', $testimonial),
            route('admin.faqs.index'),
            route('admin.faqs.create'),
            route('admin.faqs.edit', $faq),
            route('admin.messages.index'),
            route('admin.messages.show', $message),
            route('admin.settings.edit'),
            route('admin.certificate-templates.index'),
            route('admin.certificate-templates.create'),
            route('admin.certificate-templates.edit', $certificateTemplate),
            route('admin.announcements.index'),
            route('admin.announcements.create'),
            route('admin.announcements.edit', $announcement),
            route('admin.reviews.index'),
            route('admin.questions.index'),
            route('admin.support-tickets.index'),
            route('admin.support-tickets.show', $ticket),
            route('admin.email-templates.index'),
            route('admin.email-templates.edit', 'welcome'),
            route('admin.activity-log.index'),
            route('admin.live-classes.attendance.edit', $liveClass),
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)
                ->get($url)
                ->assertOk("Expected {$url} to render.");
        }
    }

    public function test_opening_a_message_marks_it_read(): void
    {
        $message = ContactMessage::factory()->create(['is_read' => false]);

        $this->actingAs($this->admin())->get(route('admin.messages.show', $message))->assertOk();

        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_the_show_routes_redirect_to_their_edit_screens(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->get(route('admin.courses.show', $course))
            ->assertRedirect(route('admin.courses.edit', $course));

        $this->actingAs($this->admin())
            ->get(route('admin.live-classes.show', $liveClass))
            ->assertRedirect(route('admin.live-classes.edit', $liveClass));
    }
}
