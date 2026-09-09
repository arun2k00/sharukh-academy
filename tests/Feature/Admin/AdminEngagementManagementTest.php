<?php

namespace Tests\Feature\Admin;

use App\Enums\AttendanceStatus;
use App\Enums\SupportTicketStatus;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use App\Models\SupportTicket;
use App\Services\EmailTemplateRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEngagementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_posting_a_sitewide_announcement_notifies_every_student(): void
    {
        $first = $this->student();
        $second = $this->student();

        $this->actingAs($this->admin())
            ->post(route('admin.announcements.store'), [
                'title' => 'Platform update',
                'body' => 'We added a new feature.',
            ])
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('course_announcements', ['title' => 'Platform update', 'course_id' => null]);
        $this->assertSame(1, $first->fresh()->notifications()->count());
        $this->assertSame(1, $second->fresh()->notifications()->count());
    }

    public function test_a_course_scoped_announcement_only_notifies_enrolled_students(): void
    {
        $course = $this->courseWithCurriculum();
        $enrolled = $this->student();
        $outsider = $this->student();
        Enrollment::factory()->create(['user_id' => $enrolled->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin())->post(route('admin.announcements.store'), [
            'title' => 'Module 3 delayed',
            'body' => 'We are pushing back the release by a week.',
            'course_id' => $course->id,
        ]);

        $this->assertSame(1, $enrolled->fresh()->notifications()->count());
        $this->assertSame(0, $outsider->fresh()->notifications()->count());
    }

    public function test_a_student_cannot_post_an_announcement(): void
    {
        $this->actingAs($this->student())
            ->post(route('admin.announcements.store'), ['title' => 'x', 'body' => 'y'])
            ->assertForbidden();

        $this->assertDatabaseCount('course_announcements', 0);
    }

    public function test_toggling_a_review_flips_its_visibility(): void
    {
        $review = CourseReview::factory()->create(['is_approved' => true]);

        $this->actingAs($this->admin())
            ->patch(route('admin.reviews.toggle', $review))
            ->assertRedirect();

        $this->assertFalse($review->fresh()->is_approved);
    }

    public function test_a_student_cannot_moderate_reviews(): void
    {
        $review = CourseReview::factory()->create();

        $this->actingAs($this->student())
            ->patch(route('admin.reviews.toggle', $review))
            ->assertForbidden();
    }

    public function test_marking_attendance_records_present_and_absent_students(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);
        $present = $this->student();
        $absent = $this->student();
        Enrollment::factory()->create(['user_id' => $present->id, 'course_id' => $course->id]);
        Enrollment::factory()->create(['user_id' => $absent->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin())
            ->put(route('admin.live-classes.attendance.update', $liveClass), [
                'attendance' => [
                    $present->id => 'present',
                    $absent->id => 'absent',
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('live_class_attendances', [
            'live_class_id' => $liveClass->id, 'user_id' => $present->id, 'status' => AttendanceStatus::Present->value,
        ]);
        $this->assertDatabaseHas('live_class_attendances', [
            'live_class_id' => $liveClass->id, 'user_id' => $absent->id, 'status' => AttendanceStatus::Absent->value,
        ]);
    }

    public function test_re_marking_attendance_updates_rather_than_duplicates(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $admin = $this->admin();
        $this->actingAs($admin)->put(route('admin.live-classes.attendance.update', $liveClass), [
            'attendance' => [$student->id => 'absent'],
        ]);
        $this->actingAs($admin)->put(route('admin.live-classes.attendance.update', $liveClass), [
            'attendance' => [$student->id => 'present'],
        ]);

        $this->assertSame(1, LiveClassAttendance::count());
        $this->assertDatabaseHas('live_class_attendances', ['user_id' => $student->id, 'status' => 'present']);
    }

    public function test_admin_can_reply_to_and_change_status_of_a_support_ticket(): void
    {
        $ticket = SupportTicket::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.support-tickets.replies.store', $ticket), ['message' => 'We are looking into it.'])
            ->assertRedirect();

        // Replying to an open ticket moves it to in-progress automatically.
        $this->assertSame(SupportTicketStatus::InProgress, $ticket->fresh()->status);

        $this->actingAs($this->admin())
            ->patch(route('admin.support-tickets.status', $ticket), ['status' => 'resolved'])
            ->assertRedirect();

        $this->assertSame(SupportTicketStatus::Resolved, $ticket->fresh()->status);
    }

    public function test_a_student_cannot_reach_the_admin_support_desk(): void
    {
        $ticket = SupportTicket::factory()->create();

        $this->actingAs($this->student())
            ->get(route('admin.support-tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_editing_an_email_template_changes_what_is_sent(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.email-templates.update', 'welcome'), [
                'subject' => 'Hey {{ student_name }}!',
                'body' => 'Glad to have you at {{ academy_name }}.',
            ])
            ->assertRedirect(route('admin.email-templates.index'));

        $rendered = app(EmailTemplateRenderer::class)->render('welcome', [
            'student_name' => 'Priya',
            'academy_name' => 'Oacer Academy',
        ]);

        $this->assertSame('Hey Priya!', $rendered['subject']);
        $this->assertSame('Glad to have you at Oacer Academy.', $rendered['body']);
    }

    public function test_an_unknown_email_template_key_is_not_editable(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.email-templates.edit', 'not-a-real-template'))
            ->assertNotFound();
    }

    public function test_template_variables_are_only_substituted_never_executed(): void
    {
        $this->actingAs($this->admin())->put(route('admin.email-templates.update', 'welcome'), [
            'subject' => 'Welcome',
            'body' => 'Hello {{ student_name }}, your file is <?php system("id"); ?> safe.',
        ]);

        $rendered = app(EmailTemplateRenderer::class)->render('welcome', ['student_name' => 'Test <script>']);

        // The token is substituted verbatim — no PHP evaluation, no escaping
        // surprises, since the value is only ever placed into a Blade-escaped view.
        $this->assertStringContainsString('Hello Test <script>, your file is', $rendered['body']);
        $this->assertStringContainsString('<?php system("id"); ?>', $rendered['body']);
    }

    public function test_key_admin_actions_are_written_to_the_activity_log(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Logged action',
            'body' => 'Body text.',
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'user_id' => $admin->id,
            'action' => 'announcement.created',
        ]);
    }

    public function test_a_student_cannot_view_the_activity_log(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.activity-log.index'))
            ->assertForbidden();
    }
}
