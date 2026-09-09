<?php

namespace Tests\Feature;

use App\Enums\SupportTicketStatus;
use App\Models\CourseQuestion;
use App\Models\CourseQuestionReply;
use App\Models\Enrollment;
use App\Models\LessonNote;
use App\Models\SupportTicket;
use App\Services\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Notes (§15), bookmarks (§16), Q&A (§14), reviews (§22) and support (§25) —
 * each is scoped to its owner and, where relevant, to enrolment in the course.
 */
class StudentEngagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_new_student_page_renders(): void
    {
        $student = $this->student();
        $course = $this->courseWithCurriculum();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        foreach ([
            route('bookmarks.index'),
            route('notifications.index'),
            route('support.index'),
            route('support.create'),
            route('attendance.index'),
            route('questions.index', $course),
        ] as $url) {
            $this->actingAs($student)->get($url)->assertOk("Expected {$url} to render.");
        }
    }

    public function test_a_student_bookmarks_and_unbookmarks_a_lesson(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);
        $lesson = $this->firstLesson($course);

        $this->actingAs($student)->post(route('lessons.bookmark', [$course, $lesson]))->assertRedirect();
        $this->assertDatabaseHas('lesson_bookmarks', ['user_id' => $student->id, 'lesson_id' => $lesson->id]);

        $this->actingAs($student)->post(route('lessons.bookmark', [$course, $lesson]))->assertRedirect();
        $this->assertDatabaseMissing('lesson_bookmarks', ['user_id' => $student->id, 'lesson_id' => $lesson->id]);
    }

    public function test_a_student_cannot_bookmark_a_lesson_they_cannot_access(): void
    {
        $course = $this->courseWithCurriculum();
        $lesson = $this->firstLesson($course);

        $this->actingAs($this->student())
            ->post(route('lessons.bookmark', [$course, $lesson]))
            ->assertForbidden();

        $this->assertDatabaseCount('lesson_bookmarks', 0);
    }

    public function test_a_student_creates_and_deletes_their_own_note(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);
        $lesson = $this->firstLesson($course);

        $this->actingAs($student)
            ->post(route('notes.store', [$course, $lesson]), [
                'content' => 'Remember to review this section.',
                'video_timestamp_seconds' => 754,
            ])
            ->assertRedirect();

        $note = LessonNote::firstOrFail();
        $this->assertSame($student->id, $note->user_id);
        $this->assertSame('12:34', $note->timestamp_label);

        $this->actingAs($student)->delete(route('notes.destroy', $note))->assertRedirect();
        $this->assertDatabaseCount('lesson_notes', 0);
    }

    public function test_a_student_cannot_read_or_delete_another_students_note(): void
    {
        $course = $this->courseWithCurriculum();
        $owner = $this->student();
        Enrollment::factory()->create(['user_id' => $owner->id, 'course_id' => $course->id]);
        $lesson = $this->firstLesson($course);

        $note = LessonNote::create([
            'user_id' => $owner->id,
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            'content' => 'Private note.',
        ]);

        $other = $this->student();

        $this->actingAs($other)->delete(route('notes.destroy', $note))->assertForbidden();
        $this->actingAs($other)
            ->put(route('notes.update', $note), ['content' => 'hijacked'])
            ->assertForbidden();

        $this->assertSame('Private note.', $note->fresh()->content);
    }

    public function test_notes_never_appear_on_another_students_lesson_page(): void
    {
        $course = $this->courseWithCurriculum();
        $lesson = $this->firstLesson($course);

        $owner = $this->student();
        Enrollment::factory()->create(['user_id' => $owner->id, 'course_id' => $course->id]);
        LessonNote::create([
            'user_id' => $owner->id, 'lesson_id' => $lesson->id, 'course_id' => $course->id,
            'content' => 'Something only I should see.',
        ]);

        $other = $this->student();
        Enrollment::factory()->create(['user_id' => $other->id, 'course_id' => $course->id]);

        $this->actingAs($other)
            ->get(route('learn.lesson', [$course, $lesson]))
            ->assertOk()
            ->assertDontSee('Something only I should see.');
    }

    public function test_an_enrolled_student_asks_a_question_and_can_delete_it(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->post(route('questions.store', $course), ['question' => 'How does drip content work?'])
            ->assertRedirect();

        $question = CourseQuestion::firstOrFail();

        $this->actingAs($this->student())->delete(route('questions.destroy', $question))->assertForbidden();
        $this->actingAs($student)->delete(route('questions.destroy', $question))->assertRedirect();
        $this->assertDatabaseCount('course_questions', 0);
    }

    public function test_a_non_enrolled_student_cannot_post_a_question(): void
    {
        $course = $this->courseWithCurriculum();

        $this->actingAs($this->student())
            ->post(route('questions.store', $course), ['question' => 'Can I see this?'])
            ->assertForbidden();

        $this->assertDatabaseCount('course_questions', 0);
    }

    public function test_an_admin_can_reply_and_pin_but_only_admin_can_delete_others_replies(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);
        $question = CourseQuestion::create(['course_id' => $course->id, 'user_id' => $student->id, 'question' => 'Q?']);

        $this->actingAs($this->admin())
            ->post(route('questions.replies.store', $question), ['reply' => 'Here is the answer.'])
            ->assertRedirect();

        $reply = CourseQuestionReply::firstOrFail();

        $this->actingAs($this->student())->delete(route('questions.replies.destroy', $reply))->assertForbidden();
        $this->actingAs($this->admin())->delete(route('questions.replies.destroy', $reply))->assertRedirect();
        $this->assertDatabaseCount('course_question_replies', 0);
    }

    public function test_a_student_submits_a_review_only_once_and_updates_are_upserted(): void
    {
        $course = $this->courseWithCurriculum();
        $student = $this->student();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($student)
            ->post(route('courses.reviews.store', $course), ['rating' => 4, 'review' => 'Good course.'])
            ->assertRedirect();

        $this->actingAs($student)
            ->post(route('courses.reviews.store', $course), ['rating' => 5, 'review' => 'Even better on reflection.'])
            ->assertRedirect();

        $this->assertDatabaseCount('course_reviews', 1);
        $this->assertDatabaseHas('course_reviews', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'rating' => 5,
        ]);
    }

    public function test_a_non_enrolled_student_cannot_review_a_course(): void
    {
        $course = $this->courseWithCurriculum();

        $this->actingAs($this->student())
            ->post(route('courses.reviews.store', $course), ['rating' => 5])
            ->assertForbidden();

        $this->assertDatabaseCount('course_reviews', 0);
    }

    public function test_a_student_opens_a_support_ticket_and_replies_to_it(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('support.store'), [
                'subject' => 'Cannot access my course',
                'category' => 'technical',
                'message' => 'The video will not load.',
            ])
            ->assertRedirect();

        $ticket = SupportTicket::firstOrFail();
        $this->assertSame($student->id, $ticket->user_id);

        $this->actingAs($student)
            ->post(route('support.replies.store', $ticket), ['message' => 'Still broken.'])
            ->assertRedirect();

        $this->assertDatabaseHas('support_ticket_replies', ['support_ticket_id' => $ticket->id]);
    }

    public function test_a_student_cannot_view_another_students_support_ticket(): void
    {
        $owner = $this->student();
        $ticket = SupportTicket::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($this->student())
            ->get(route('support.show', $ticket))
            ->assertForbidden();
    }

    public function test_replying_to_a_resolved_ticket_reopens_it(): void
    {
        $student = $this->student();
        $ticket = SupportTicket::factory()->create(['user_id' => $student->id, 'status' => SupportTicketStatus::Resolved]);

        $this->actingAs($student)
            ->post(route('support.replies.store', $ticket), ['message' => 'One more thing.'])
            ->assertRedirect();

        $this->assertSame(SupportTicketStatus::Open, $ticket->fresh()->status);
    }

    public function test_notifications_can_be_marked_read(): void
    {
        $student = $this->student();
        app(NotificationDispatcher::class)->welcome($student);

        $notification = $student->notifications()->firstOrFail();
        $this->assertNull($notification->read_at);

        $this->actingAs($student)
            ->post(route('notifications.read', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_a_student_cannot_mark_another_students_notification_read(): void
    {
        $owner = $this->student();
        app(NotificationDispatcher::class)->welcome($owner);
        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($this->student())
            ->post(route('notifications.read', $notification))
            ->assertForbidden();
    }
}
