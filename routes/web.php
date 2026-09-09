<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\CareerPathController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\CoursePreviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveClassCalendarController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseCatalogController::class, 'show'])->name('courses.show');
Route::get('/courses/{course:slug}/preview/{lesson:slug}', CoursePreviewController::class)
    ->scopeBindings()
    ->name('courses.preview');

Route::get('/career-paths', [CareerPathController::class, 'index'])->name('career-paths.index');
Route::get('/career-paths/{careerPath:slug}', [CareerPathController::class, 'show'])->name('career-paths.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', ContactMessageController::class)
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::get('/verify-certificate/{certificate:certificate_number}', CertificateVerificationController::class)
    ->name('certificates.verify');
Route::get('/verify-certificate/{certificate:certificate_number}/image', Student\CertificateImageController::class)
    ->name('certificates.verify-image');

// Live class detail doubles as its own public "share" preview page (§5):
// guests and non-enrolled students see a restricted view with no Zoom URL;
// only an enrolled, authenticated student is ever offered the join action.
Route::get('/live-classes/{liveClass:slug}', [Student\LiveClassController::class, 'show'])->name('live-classes.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('register', [Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [Auth\RegisteredUserController::class, 'store']);

    Route::get('login', [Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [Auth\AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');

    Route::get('forgot-password', [Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [Auth\PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');

    Route::get('reset-password/{token}', [Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [Auth\NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('verify-email', Auth\EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', Auth\VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', Auth\EmailVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [Auth\ConfirmablePasswordController::class, 'create'])->name('password.confirm');
    Route::post('confirm-password', [Auth\ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Student area
|--------------------------------------------------------------------------
|
| Email verification is only enforced when the academy has working outgoing
| mail; see `academy.require_email_verification`.
|
*/

$studentMiddleware = array_values(array_filter([
    'auth',
    config('academy.require_email_verification') ? 'verified' : null,
]));

Route::middleware($studentMiddleware)->group(function (): void {
    Route::get('dashboard', Student\DashboardController::class)->name('dashboard');
    Route::get('my-courses', Student\MyCourseController::class)->name('my-courses');

    Route::post('courses/{course:slug}/enroll', Student\EnrollmentController::class)->name('courses.enroll');
    Route::post('courses/{course:slug}/reviews', Student\CourseReviewController::class)->name('courses.reviews.store');

    // Learning
    Route::get('learn/{course:slug}', [Student\LearnController::class, 'show'])->name('learn.course');
    Route::get('learn/{course:slug}/lesson/{lesson:slug}', [Student\LearnController::class, 'lesson'])
        ->scopeBindings()
        ->name('learn.lesson');

    Route::post('learn/{course:slug}/lesson/{lesson:slug}/completion', [Student\LessonCompletionController::class, 'store'])
        ->scopeBindings()
        ->name('lessons.complete');
    Route::delete('learn/{course:slug}/lesson/{lesson:slug}/completion', [Student\LessonCompletionController::class, 'destroy'])
        ->scopeBindings()
        ->name('lessons.uncomplete');
    Route::post('learn/{course:slug}/lesson/{lesson:slug}/watch-progress', Student\LessonWatchProgressController::class)
        ->scopeBindings()
        ->middleware('throttle:progress')
        ->name('lessons.watch');
    Route::post('learn/{course:slug}/lesson/{lesson:slug}/bookmark', Student\LessonBookmarkController::class)
        ->scopeBindings()
        ->name('lessons.bookmark');

    // Private lesson notes
    Route::post('learn/{course:slug}/lesson/{lesson:slug}/notes', [Student\LessonNoteController::class, 'store'])
        ->scopeBindings()
        ->name('notes.store');
    Route::put('notes/{note}', [Student\LessonNoteController::class, 'update'])->name('notes.update');
    Route::delete('notes/{note}', [Student\LessonNoteController::class, 'destroy'])->name('notes.destroy');

    Route::get('bookmarks', Student\BookmarkListController::class)->name('bookmarks.index');

    // Course Q&A
    Route::get('learn/{course:slug}/questions', [Student\CourseQuestionController::class, 'index'])->name('questions.index');
    Route::post('learn/{course:slug}/questions', [Student\CourseQuestionController::class, 'store'])->name('questions.store');
    Route::delete('questions/{question}', [Student\CourseQuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('questions/{question}/replies', Student\CourseQuestionReplyController::class)->name('questions.replies.store');
    Route::delete('question-replies/{reply}', Student\CourseQuestionReplyDestroyController::class)->name('questions.replies.destroy');

    // Protected downloads
    Route::get('resources/lessons/{resource}', Student\LessonResourceDownloadController::class)->name('resources.lesson');
    Route::get('resources/live-classes/{resource}', Student\LiveClassResourceDownloadController::class)->name('resources.live-class');

    // Live classes
    Route::get('live-classes', [Student\LiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('live-classes/{liveClass:slug}/join', Student\LiveClassJoinController::class)->name('live-classes.join');
    Route::get('live-classes/{liveClass:slug}/calendar.ics', LiveClassCalendarController::class)->name('live-classes.calendar');
    Route::get('my-attendance', Student\AttendanceController::class)->name('attendance.index');

    // Certificates
    Route::get('certificates', [Student\CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/{certificate:certificate_number}', [Student\CertificateController::class, 'show'])->name('certificates.show');
    Route::get('certificates/{certificate:certificate_number}/print', [Student\CertificateController::class, 'print'])->name('certificates.print');
    Route::get('certificates/{certificate:certificate_number}/image', Student\CertificateImageController::class)->name('certificates.image');

    // Notifications
    Route::get('notifications', [Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [Student\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [Student\NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Support
    Route::get('support', [Student\SupportTicketController::class, 'index'])->name('support.index');
    Route::get('support/create', [Student\SupportTicketController::class, 'create'])->name('support.create');
    Route::post('support', [Student\SupportTicketController::class, 'store'])->name('support.store');
    Route::get('support/{ticket}', [Student\SupportTicketController::class, 'show'])->name('support.show');
    Route::post('support/{ticket}/replies', Student\SupportTicketReplyController::class)->name('support.replies.store');

    // Profile
    Route::get('profile', [Student\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [Student\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile/avatar', Student\AvatarController::class)->name('profile.avatar.destroy');
    Route::put('profile/password', Student\PasswordController::class)->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::resource('courses', Admin\CourseController::class);
    Route::patch('courses/{course}/status', Admin\CourseStatusController::class)->name('courses.status');
    Route::patch('courses/{course}/restore', Admin\CourseRestoreController::class)
        ->withTrashed()
        ->name('courses.restore');

    // Curriculum builder
    Route::get('courses/{course}/curriculum', Admin\CurriculumController::class)->name('courses.curriculum');

    Route::post('courses/{course}/modules', [Admin\CourseModuleController::class, 'store'])->name('modules.store');
    Route::get('modules/{module}/edit', [Admin\CourseModuleController::class, 'edit'])->name('modules.edit');
    Route::put('modules/{module}', [Admin\CourseModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{module}', [Admin\CourseModuleController::class, 'destroy'])->name('modules.destroy');
    Route::patch('modules/{module}/move', Admin\CourseModuleOrderController::class)->name('modules.move');

    Route::get('courses/{course}/lessons/create', [Admin\LessonController::class, 'create'])->name('lessons.create');
    Route::post('courses/{course}/lessons', [Admin\LessonController::class, 'store'])->name('lessons.store');
    Route::get('lessons/{lesson}/edit', [Admin\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [Admin\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [Admin\LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::patch('lessons/{lesson}/move', Admin\LessonOrderController::class)->name('lessons.move');

    Route::post('lessons/{lesson}/resources', [Admin\LessonResourceController::class, 'store'])->name('lessons.resources.store');
    Route::delete('lesson-resources/{resource}', [Admin\LessonResourceController::class, 'destroy'])->name('lessons.resources.destroy');

    Route::resource('live-classes', Admin\LiveClassController::class)->parameters(['live-classes' => 'liveClass']);
    Route::post('live-classes/{liveClass}/resources', [Admin\LiveClassResourceController::class, 'store'])->name('live-classes.resources.store');
    Route::delete('live-class-resources/{resource}', [Admin\LiveClassResourceController::class, 'destroy'])->name('live-classes.resources.destroy');
    Route::get('live-classes/{liveClass}/attendance', [Admin\LiveClassAttendanceController::class, 'edit'])->name('live-classes.attendance.edit');
    Route::put('live-classes/{liveClass}/attendance', [Admin\LiveClassAttendanceController::class, 'update'])->name('live-classes.attendance.update');

    Route::resource('students', Admin\StudentController::class);

    Route::get('enrollments', [Admin\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('enrollments/create', [Admin\EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('enrollments', [Admin\EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::patch('enrollments/{enrollment}', [Admin\EnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('enrollments/{enrollment}', [Admin\EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    Route::get('certificates', [Admin\CertificateController::class, 'index'])->name('certificates.index');
    Route::delete('certificates/{certificate}', [Admin\CertificateController::class, 'destroy'])->name('certificates.destroy');

    Route::resource('certificate-templates', Admin\CertificateTemplateController::class)
        ->parameters(['certificate-templates' => 'certificate_template'])
        ->except(['show']);
    Route::get('certificate-templates/{certificate_template}/preview', [Admin\CertificateTemplateController::class, 'preview'])
        ->name('certificate-templates.preview');

    Route::resource('categories', Admin\CategoryController::class)->except(['show']);
    Route::resource('career-paths', Admin\CareerPathController::class)
        ->parameters(['career-paths' => 'career_path'])
        ->except(['show']);
    Route::resource('instructors', Admin\InstructorController::class)->except(['show']);
    Route::resource('testimonials', Admin\TestimonialController::class)->except(['show']);
    Route::resource('faqs', Admin\FaqController::class)->except(['show']);
    Route::resource('announcements', Admin\CourseAnnouncementController::class)->except(['show']);

    Route::get('reviews', [Admin\CourseReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/toggle', [Admin\CourseReviewController::class, 'toggle'])->name('reviews.toggle');
    Route::delete('reviews/{review}', [Admin\CourseReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('questions', [Admin\CourseQuestionController::class, 'index'])->name('questions.index');

    Route::get('support-tickets', [Admin\SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('support-tickets/{ticket}', [Admin\SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::patch('support-tickets/{ticket}/status', [Admin\SupportTicketController::class, 'updateStatus'])->name('support-tickets.status');
    Route::post('support-tickets/{ticket}/replies', Admin\SupportTicketReplyController::class)->name('support-tickets.replies.store');

    Route::get('email-templates', [Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('email-templates/{emailTemplate}/edit', [Admin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('email-templates/{emailTemplate}', [Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');

    Route::get('activity-log', Admin\ActivityLogController::class)->name('activity-log.index');

    Route::get('messages', [Admin\ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [Admin\ContactMessageController::class, 'show'])->name('messages.show');
    Route::delete('messages/{message}', [Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});
