<?php

namespace Database\Seeders;

use App\Enums\LiveClassStatus;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\CourseQuestion;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonBookmark;
use App\Models\LessonNote;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Populates the engagement features added in the advanced-features phase
 * (§12–16, §22, §25) with realistic demo content, so a fresh install shows
 * working screens rather than empty states everywhere. Runs after
 * DemoAccountSeeder, which is what creates the enrolments and progress this
 * seeder attaches to.
 */
class EngagementSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::query()->published()->orderBy('id')->get();

        if ($courses->isEmpty()) {
            return;
        }

        $admin = User::firstWhere('email', 'admin@oaceracademy.test');

        $students = User::query()
            ->whereIn('email', [
                'meera@example.test', 'jude@example.test', 'rohit@example.test',
                'fiona@example.test', 'kwame@example.test', 'sara@example.test', 'tom@example.test',
            ])
            ->get()
            ->keyBy('email');

        $meera = $students['meera@example.test'] ?? null;
        $jude = $students['jude@example.test'] ?? null;
        $rohit = $students['rohit@example.test'] ?? null;
        $fiona = $students['fiona@example.test'] ?? null;
        $kwame = $students['kwame@example.test'] ?? null;
        $tom = $students['tom@example.test'] ?? null;

        if ($admin === null || $meera === null) {
            return;
        }

        $this->seedAnnouncements($admin, $courses);
        $this->seedReviews($courses, $meera, $jude, $rohit, $fiona, $kwame);
        $this->seedQuestions($admin, $courses, $meera, $jude, $fiona);
        $this->seedNotesAndBookmarks($courses, $meera, $jude, $fiona);
        $this->seedSupportTickets($admin, $tom, $rohit, $meera);
        $this->seedLiveClassAttendance($admin);
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    private function seedAnnouncements(User $admin, Collection $courses): void
    {
        CourseAnnouncement::query()->delete();

        CourseAnnouncement::create([
            'course_id' => null,
            'title' => 'Notifications and your dashboard have a new look',
            'body' => "We've added a notification centre, bookmarks and lesson notes across every course. Look for the bell icon in the header — you'll find updates about live classes, certificates and replies to your questions there from now on.",
            'created_by' => $admin->id,
        ]);

        if ($courses->has(0)) {
            CourseAnnouncement::create([
                'course_id' => $courses[0]->id,
                'title' => 'Bonus swipe-file added to Module 2',
                'body' => 'A downloadable swipe file with real campaign briefs has been added to the second module. Find it under the resources on the relevant lesson.',
                'created_by' => $admin->id,
            ]);
        }

        if ($courses->has(3)) {
            CourseAnnouncement::create([
                'course_id' => $courses[3]->id,
                'title' => 'Extra live Q&A session added',
                'body' => 'Based on questions coming in, an extra live session has been scheduled. Check the live classes tab for the date and add it to your calendar.',
                'created_by' => $admin->id,
            ]);
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    private function seedReviews(
        Collection $courses,
        User $meera,
        ?User $jude,
        ?User $rohit,
        ?User $fiona,
        ?User $kwame,
    ): void {
        CourseReview::query()->delete();

        if ($courses->has(0)) {
            CourseReview::create([
                'course_id' => $courses[0]->id, 'user_id' => $meera->id,
                'rating' => 5, 'review' => 'Practical from the first lesson. I used the targeting framework on a live campaign in week two and the drop in cost-per-lead was immediate.',
                'is_approved' => true,
            ]);

            if ($kwame !== null) {
                CourseReview::create([
                    'course_id' => $courses[0]->id, 'user_id' => $kwame->id,
                    'rating' => 4, 'review' => "Good pace and the live sessions are worth attending. Wish there was more on attribution but that's a small gap.",
                    'is_approved' => true,
                ]);
            }
        }

        if ($courses->has(1) && $jude !== null) {
            CourseReview::create([
                'course_id' => $courses[1]->id, 'user_id' => $jude->id,
                'rating' => 5, 'review' => 'Exactly what I needed to take a brand identity project from brief to delivery with confidence.',
                'is_approved' => true,
            ]);
        }

        if ($courses->has(3) && $fiona !== null) {
            CourseReview::create([
                'course_id' => $courses[3]->id, 'user_id' => $fiona->id,
                'rating' => 4, 'review' => 'Clear structure and the templates saved me hours. Some sections assume tools I had to look up separately.',
                'is_approved' => true,
            ]);
        }

        // Left unapproved on purpose, so the admin moderation queue (§22) has something in it.
        if ($courses->has(2) && $rohit !== null) {
            CourseReview::create([
                'course_id' => $courses[2]->id, 'user_id' => $rohit->id,
                'rating' => 3, 'review' => "Solid content but I'm only halfway through — marking as in progress for now.",
                'is_approved' => false,
            ]);
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    private function seedQuestions(
        User $admin,
        Collection $courses,
        User $meera,
        ?User $jude,
        ?User $fiona,
    ): void {
        CourseQuestion::query()->delete();

        if ($courses->has(0)) {
            $lesson = $this->firstLesson($courses[0]);

            $question = CourseQuestion::create([
                'course_id' => $courses[0]->id,
                'lesson_id' => $lesson?->id,
                'user_id' => $meera->id,
                'question' => 'For the targeting exercise, should the lookalike audience be built from purchasers or from everyone who added to cart?',
            ]);

            $question->replies()->create([
                'user_id' => $admin->id,
                'reply' => 'Start from purchasers if you have at least a few hundred — it converts better even though the audience is smaller. Fall back to add-to-cart only if purchaser volume is too low to build a reliable lookalike.',
                'is_pinned' => true,
            ]);
        }

        if ($courses->has(1) && $jude !== null) {
            CourseQuestion::create([
                'course_id' => $courses[1]->id,
                'lesson_id' => null,
                'user_id' => $jude->id,
                'question' => 'Is there a recommended file structure for handing off the final identity system to a client?',
            ]);
        }

        if ($courses->has(3) && $fiona !== null) {
            $question = CourseQuestion::create([
                'course_id' => $courses[3]->id,
                'lesson_id' => $this->firstLesson($courses[3])?->id,
                'user_id' => $fiona->id,
                'question' => 'Which template should I start from if the project is a rebrand rather than a brand-new identity?',
            ]);

            $question->replies()->create([
                'user_id' => $admin->id,
                'reply' => "Start from the same template — the rebrand-specific guidance is in the lesson notes further into the module, it just isn't a separate template.",
                'is_pinned' => false,
            ]);
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    private function seedNotesAndBookmarks(Collection $courses, User $meera, ?User $jude, ?User $fiona): void
    {
        LessonNote::query()->delete();
        LessonBookmark::query()->delete();

        if ($courses->has(0)) {
            $lessons = $this->publishedLessons($courses[0]);

            if ($lessons->isNotEmpty()) {
                LessonNote::create([
                    'user_id' => $meera->id, 'lesson_id' => $lessons->first()->id, 'course_id' => $courses[0]->id,
                    'content' => 'Re-watch this before the live session — the audience-overlap point at the start is easy to miss.',
                    'video_timestamp_seconds' => 145,
                ]);

                LessonBookmark::create([
                    'user_id' => $meera->id, 'lesson_id' => $lessons->first()->id, 'course_id' => $courses[0]->id,
                ]);
            }

            if ($lessons->count() > 1) {
                LessonNote::create([
                    'user_id' => $meera->id, 'lesson_id' => $lessons[1]->id, 'course_id' => $courses[0]->id,
                    'content' => 'Budget split example: 70/30 was for a launch campaign, not evergreen. Check before copying the ratio.',
                    'video_timestamp_seconds' => 512,
                ]);
            }
        }

        if ($courses->has(1) && $jude !== null) {
            $lessons = $this->publishedLessons($courses[1]);

            if ($lessons->isNotEmpty()) {
                LessonNote::create([
                    'user_id' => $jude->id, 'lesson_id' => $lessons->first()->id, 'course_id' => $courses[1]->id,
                    'content' => 'Good reference for the client presentation deck structure.',
                    'video_timestamp_seconds' => null,
                ]);
            }
        }

        if ($courses->has(3) && $fiona !== null) {
            $lessons = $this->publishedLessons($courses[3]);

            if ($lessons->isNotEmpty()) {
                LessonBookmark::create([
                    'user_id' => $fiona->id, 'lesson_id' => $lessons->first()->id, 'course_id' => $courses[3]->id,
                ]);
            }
        }
    }

    private function seedSupportTickets(User $admin, ?User $tom, ?User $rohit, User $meera): void
    {
        SupportTicket::query()->delete();

        if ($tom !== null) {
            SupportTicket::create([
                'user_id' => $tom->id,
                'subject' => 'Invoice for enrolment needed',
                'category' => 'billing',
                'message' => 'Could you send an invoice with VAT details for my recent enrolment? My employer needs it for reimbursement.',
                'status' => 'open',
            ]);
        }

        if ($rohit !== null) {
            $ticket = SupportTicket::create([
                'user_id' => $rohit->id,
                'subject' => 'Video keeps buffering on lesson 3',
                'category' => 'technical',
                'message' => 'The video on lesson three of the negotiation course keeps buffering every few seconds even though everything else on my connection is fine.',
                'status' => 'in_progress',
            ]);

            $ticket->replies()->create([
                'user_id' => $admin->id,
                'message' => "Thanks for flagging this — could you tell us which browser and roughly what time (with timezone) you saw it? We'll check the video host's logs for that window.",
            ]);
        }

        $resolved = SupportTicket::create([
            'user_id' => $meera->id,
            'subject' => 'Certificate name has a typo',
            'category' => 'course_content',
            'message' => 'My certificate shows "Meera Sundaraman" instead of "Meera Sundaram" — can this be corrected?',
            'status' => 'resolved',
        ]);

        $resolved->replies()->create([
            'user_id' => $admin->id,
            'message' => 'Apologies for that — your account name has been corrected and the certificate will regenerate with the right spelling next time you view it.',
        ]);
    }

    private function seedLiveClassAttendance(User $admin): void
    {
        LiveClassAttendance::query()->delete();

        $pastClasses = LiveClass::query()->where('status', LiveClassStatus::Completed)->get();

        foreach ($pastClasses as $liveClass) {
            $enrolledStudentIds = Enrollment::query()
                ->where('course_id', $liveClass->course_id)
                ->pluck('user_id');

            foreach ($enrolledStudentIds->take(3)->values() as $index => $userId) {
                LiveClassAttendance::create([
                    'live_class_id' => $liveClass->id,
                    'user_id' => $userId,
                    'status' => $index === 2 ? 'absent' : 'present',
                    'marked_at' => $liveClass->scheduled_at->copy()->addHour(),
                    'marked_by' => $admin->id,
                ]);
            }
        }
    }

    private function firstLesson(Course $course): ?Lesson
    {
        return $this->publishedLessons($course)->first();
    }

    /**
     * @return Collection<int, Lesson>
     */
    private function publishedLessons(Course $course): Collection
    {
        return Lesson::query()
            ->where('lessons.course_id', $course->id)
            ->where('lessons.is_published', true)
            ->inCurriculumOrder()
            ->get();
    }
}
