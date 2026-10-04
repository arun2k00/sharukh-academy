<?php

namespace Database\Seeders;

use App\Enums\EnrollmentSource;
use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\User;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demonstration accounts and the enrollment history behind them.
 *
 * Progress is recorded through the real services rather than written straight
 * to the tables, so the seeded state matches what the application produces —
 * including the certificates that completion triggers.
 */
class DemoAccountSeeder extends Seeder
{
    /**
     * Passwords for the seeded demo accounts, read from the environment so no
     * real credential is ever committed. Set DEMO_SEED_PASSWORD locally to use
     * your own; the fallback is deliberately obvious and useless in production,
     * where this seeder should not be run at all.
     */
    private function adminPassword(): string
    {
        return (string) env('DEMO_SEED_ADMIN_PASSWORD', env('DEMO_SEED_PASSWORD', 'password'));
    }

    private function demoPassword(): string
    {
        return (string) env('DEMO_SEED_PASSWORD', 'password');
    }

    public function run(EnrollmentService $enrollments, ProgressService $progress): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@aixone.site'],
            [
                'name' => 'Arun Menon',
                'password' => Hash::make($this->adminPassword()),
                'email_verified_at' => now(),
            ],
        );

        // `role` is not mass assignable, so it is set explicitly.
        $admin->forceFill(['role' => UserRole::Admin])->save();

        $trainer = User::updateOrCreate(
            ['email' => 'trainer@aixone.site'],
            [
                'name' => 'Sharuk Shaik',
                'password' => Hash::make($this->demoPassword()),
                'email_verified_at' => now(),
            ],
        );

        $trainer->forceFill(['role' => UserRole::Trainer])->save();

        // CatalogSeeder runs before this one and already created the matching
        // "Sharuk Shaik" instructor profile — link it so the demo Trainer
        // account actually sees courses in the Tutor dashboard.
        Instructor::where('slug', 'sharuk-shaik')->update(['user_id' => $trainer->id]);

        $students = collect([
            ['name' => 'Meera Sundaram', 'email' => 'student@aixone.site'],
            ['name' => 'Jude Mensah', 'email' => 'jude@example.test'],
            ['name' => 'Rohit Bansal', 'email' => 'rohit@example.test'],
            ['name' => 'Fiona Clarke', 'email' => 'fiona@example.test'],
            ['name' => 'Kwame Boateng', 'email' => 'kwame@example.test'],
            ['name' => 'Sara Iqbal', 'email' => 'sara@example.test'],
            ['name' => 'Tom Whitfield', 'email' => 'tom@example.test'],
        ])->map(function (array $row): User {
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make($this->demoPassword()),
                    'email_verified_at' => now(),
                ],
            );

            $user->forceFill(['role' => UserRole::Student])->save();

            return $user;
        });

        $courses = Course::query()->published()->with('lessons')->get();

        if ($courses->isEmpty()) {
            return;
        }

        // A spread of states: fully complete, part way through, and just started.
        $plan = [
            0 => [['course' => 0, 'complete' => 'all'], ['course' => 3, 'complete' => 'half']],
            1 => [['course' => 1, 'complete' => 'all'], ['course' => 5, 'complete' => 'few']],
            2 => [['course' => 2, 'complete' => 'half']],
            3 => [['course' => 3, 'complete' => 'all'], ['course' => 4, 'complete' => 'few']],
            4 => [['course' => 0, 'complete' => 'half']],
            5 => [['course' => 4, 'complete' => 'few']],
            6 => [['course' => 5, 'complete' => 'none']],
        ];

        foreach ($plan as $studentIndex => $entries) {
            $student = $students[$studentIndex] ?? null;

            if ($student === null) {
                continue;
            }

            foreach ($entries as $entry) {
                $course = $courses[$entry['course']] ?? null;

                if ($course === null) {
                    continue;
                }

                $enrollment = $enrollments->enroll(
                    $student,
                    $course,
                    $course->isFree() ? EnrollmentSource::Free : EnrollmentSource::Admin,
                );

                $lessons = Lesson::query()
                    ->where('lessons.course_id', $course->id)
                    ->where('lessons.is_published', true)
                    ->inCurriculumOrder()
                    ->get();

                $take = match ($entry['complete']) {
                    'all' => $lessons->count(),
                    'half' => (int) ceil($lessons->count() / 2),
                    'few' => min(2, $lessons->count()),
                    default => 0,
                };

                foreach ($lessons->take($take) as $lesson) {
                    $progress->markComplete($student, $lesson);
                }

                if ($take > 0 && $take < $lessons->count()) {
                    $enrollments->recordAccess($enrollment, $lessons[$take]);
                }
            }
        }

        ContactMessage::query()->delete();

        ContactMessage::create([
            'name' => 'Rahul Shah',
            'email' => 'rahul@example.test',
            'subject' => 'Corporate enrollment for six people',
            'message' => "Hello,\n\nWe would like to put six of our data team through the SQL Server and Snowflake programmes in the next quarter. Do you offer a group rate, and can the live sessions be scheduled for a single cohort?\n\nThank you,\nRahul",
        ]);

        ContactMessage::create([
            'name' => 'Nadia Haddad',
            'email' => 'nadia@example.test',
            'subject' => 'Question before enrolling',
            'message' => 'Is the Python for Data Engineers course suitable if I already know SQL well but have never written a line of Python? I want to be able to build a full pipeline end to end but my background is entirely SQL-based reporting.',
            'is_read' => true,
        ]);
    }
}
