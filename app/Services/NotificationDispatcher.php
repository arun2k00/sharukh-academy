<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\LiveClassReminderType;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseAnnouncement;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\User;
use App\Notifications\AcademyNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Builds and sends every notification the academy raises (§7, §12, §13).
 *
 * Centralising the copy and audience-selection here means a controller only
 * has to say *what happened*; this class decides who hears about it and how.
 * Every send is synchronous — see `AcademyNotification` for why.
 */
class NotificationDispatcher
{
    public function welcome(User $user): void
    {
        $user->notify(new AcademyNotification(
            kind: 'welcome',
            title: 'Welcome to '.config('academy.name'),
            body: 'Your account is ready. Explore the catalogue and start your first course.',
            url: route('courses.index'),
            mailTemplateKey: 'welcome',
            mailVariables: ['student_name' => $user->name, 'academy_name' => config('academy.name')],
        ));
    }

    public function enrollmentConfirmed(User $user, Course $course): void
    {
        $user->notify(new AcademyNotification(
            kind: 'enrollment_confirmed',
            title: 'You are enrolled in '.$course->title,
            body: 'Your course is ready — pick up the first lesson whenever you like.',
            url: route('learn.course', $course),
            mailTemplateKey: 'enrollment_confirmation',
            mailVariables: [
                'student_name' => $user->name,
                'course_name' => $course->title,
                'academy_name' => config('academy.name'),
            ],
        ));
    }

    public function certificateIssued(User $user, Certificate $certificate): void
    {
        $user->notify(new AcademyNotification(
            kind: 'certificate_issued',
            title: 'Your certificate is ready',
            body: 'You completed '.$certificate->course_title.'. Your certificate is ready to view and share.',
            url: route('certificates.show', $certificate),
            mailTemplateKey: 'certificate_issued',
            mailVariables: [
                'student_name' => $user->name,
                'course_name' => $certificate->course_title,
                'academy_name' => config('academy.name'),
            ],
        ));
    }

    public function newLiveClassScheduled(LiveClass $liveClass): void
    {
        Notification::send($this->enrolledStudents($liveClass->course_id), new AcademyNotification(
            kind: 'new_live_class',
            title: 'New live class: '.$liveClass->title,
            body: 'Scheduled for '.$liveClass->scheduled_at->format('j F, g:ia').' — '.$liveClass->course->title,
            url: route('live-classes.show', $liveClass),
        ));
    }

    public function liveClassReminder(LiveClass $liveClass, LiveClassReminderType $type): void
    {
        [$title, $body] = match ($type) {
            LiveClassReminderType::DayBefore => [
                'Tomorrow: '.$liveClass->title,
                $liveClass->title.' starts tomorrow at '.$liveClass->scheduled_at->format('g:ia').'.',
            ],
            LiveClassReminderType::HourBefore => [
                'Starting in an hour: '.$liveClass->title,
                $liveClass->title.' starts at '.$liveClass->scheduled_at->format('g:ia').', about an hour from now.',
            ],
            LiveClassReminderType::StartingSoon => [
                'Starting soon: '.$liveClass->title,
                $liveClass->title.' starts in a few minutes. Join when you are ready.',
            ],
            LiveClassReminderType::LiveNow => [
                $liveClass->title.' is live now',
                'The session has started — join whenever you are ready.',
            ],
            default => ['Live class update: '.$liveClass->title, 'There is an update to '.$liveClass->title.'.'],
        };

        $recipients = $this->enrolledStudents($liveClass->course_id);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new AcademyNotification(
            kind: 'live_class_reminder',
            title: $title,
            body: $body,
            url: route('live-classes.show', $liveClass),
            mailTemplateKey: 'live_class_reminder',
            mailVariables: [
                'live_class_name' => $liveClass->title,
                'live_class_date' => $liveClass->scheduled_at->format('j F Y, g:ia'),
                'academy_name' => config('academy.name'),
                // `student_name` is filled in per-recipient by the mailable
                // when Laravel resolves `toMail` for each notifiable.
                'student_name' => '',
            ],
        ));
    }

    public function recordingAvailable(LiveClass $liveClass): void
    {
        Notification::send($this->enrolledStudents($liveClass->course_id), new AcademyNotification(
            kind: 'recording_available',
            title: 'Recording ready: '.$liveClass->title,
            body: 'The recording for '.$liveClass->title.' is now available.',
            url: route('live-classes.show', $liveClass),
        ));
    }

    public function newLessonAvailable(Lesson $lesson): void
    {
        Notification::send($this->enrolledStudents($lesson->course_id), new AcademyNotification(
            kind: 'new_lesson',
            title: 'New lesson: '.$lesson->title,
            body: 'A new lesson was added to '.$lesson->course->title.'.',
            url: route('learn.lesson', [$lesson->course, $lesson]),
        ));
    }

    public function announcementPosted(CourseAnnouncement $announcement): void
    {
        $recipients = $announcement->isSitewide()
            ? User::query()->students()->get()
            : $this->enrolledStudents((int) $announcement->course_id);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new AcademyNotification(
            kind: 'announcement',
            title: $announcement->title,
            body: $announcement->body,
            url: $announcement->isSitewide() ? route('dashboard') : route('learn.course', $announcement->course_id),
        ));
    }

    public function accessExpiringSoon(Enrollment $enrollment, int $daysLeft): void
    {
        $enrollment->loadMissing(['user', 'course']);

        $enrollment->user->notify(new AcademyNotification(
            kind: 'access_expiring',
            title: 'Your access to '.$enrollment->course->title.' expires soon',
            body: 'Your enrolment ends in '.$daysLeft.' '.str('day')->plural($daysLeft).'. Finish up any lessons you want to keep.',
            url: route('learn.course', $enrollment->course),
        ));
    }

    /**
     * @return Collection<int, User>
     */
    private function enrolledStudents(int $courseId): Collection
    {
        return User::query()
            ->whereHas('enrollments', fn ($query) => $query
                ->where('course_id', $courseId)
                ->whereIn('status', [EnrollmentStatus::Active, EnrollmentStatus::Completed]))
            ->get();
    }
}
