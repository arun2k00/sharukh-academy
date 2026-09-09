<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\LessonType;
use App\Enums\LiveClassStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\Testimonial;
use Database\Seeders\Support\DemoFileFactory;
use Database\Seeders\Support\DemoThumbnailFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * The public catalogue: categories, instructors, courses, curriculum, live
 * classes and the marketing copy that surrounds them.
 */
class CatalogSeeder extends Seeder
{
    /**
     * Public YouTube videos used so every demo lesson has something that
     * actually plays. Replace them with the academy's own uploads.
     *
     * @var list<string>
     */
    private const DEMO_VIDEO_IDS = [
        'M7lc1UVf-VE', // Google's own embedded-player sample video.
        'aircAruvnKk', // 3Blue1Brown, "But what is a neural network?".
    ];

    public function run(): void
    {
        $categories = $this->seedCategories();
        $instructors = $this->seedInstructors();

        foreach ($this->courseDefinitions() as $position => $definition) {
            $this->seedCourse($definition, $position, $categories, $instructors);
        }

        $this->seedGeneralFaqs();
        $this->seedTestimonials();
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $rows = [
            ['name' => 'Marketing', 'slug' => 'marketing', 'description' => 'Demand generation, paid media and lifecycle work.'],
            ['name' => 'Design', 'slug' => 'design', 'description' => 'Identity, systems and the craft of visual decisions.'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Running the commercial side of a practice.'],
            ['name' => 'Analytics', 'slug' => 'analytics', 'description' => 'Turning numbers into decisions people act on.'],
            ['name' => 'Data Engineering', 'slug' => 'data-engineering', 'description' => 'Building the pipelines and warehouses everything else runs on.'],
        ];

        $categories = [];

        foreach ($rows as $index => $row) {
            $categories[$row['slug']] = Category::updateOrCreate(
                ['slug' => $row['slug']],
                [...$row, 'sort_order' => $index + 1],
            );
        }

        return $categories;
    }

    /**
     * @return array<string, Instructor>
     */
    private function seedInstructors(): array
    {
        $rows = [
            [
                'slug' => 'priya-raman',
                'name' => 'Priya Raman',
                'headline' => 'Performance marketing lead, ex-agency, 14 years in paid media',
                'is_featured' => true,
                'experience_years' => 14,
                'bio' => "I have spent fourteen years buying media for businesses that could not afford to waste it — first at a Bangalore agency running accounts for regional retail, then in-house at two consumer subscription companies.\n\nMost marketing teaching is written by people who have not had to defend a spend figure in a board meeting. This one is not. Everything here is the process I actually use: how to size a market honestly, how to build a testing plan that survives a bad quarter, and how to say no to a channel that will not work.",
                'credentials' => [
                    'Led paid acquisition for two subscription businesses through Series B',
                    'Managed cumulative ad spend above ₹40 crore',
                    'Guest lecturer, IIM Bangalore digital marketing elective',
                    'Google Ads and Meta Blueprint certified',
                ],
            ],
            [
                'slug' => 'daniel-okoro',
                'name' => 'Daniel Okoro',
                'headline' => 'Independent brand designer and design-systems consultant',
                'experience_years' => 11,
                'bio' => "I run a two-person studio. We do identity work for companies between their first and their fiftieth employee — the stage where a brand has to stop being a logo and start being a system other people can use without asking you.\n\nI teach the way I work: fewer, better decisions, documented well enough that a developer or a junior designer can carry them forward.",
                'credentials' => [
                    'Identity work for 60+ companies across Africa and Europe',
                    'Design systems consultant to three publicly listed firms',
                    'Type & Lettering award, D&AD New Blood',
                ],
            ],
            [
                'slug' => 'anita-fernandes',
                'name' => 'Anita Fernandes',
                'headline' => 'Fractional CFO for founder-led companies',
                'experience_years' => 18,
                'bio' => "I build the financial model before the company needs it, and then I make the founder able to run it without me.\n\nEighteen years of audit, corporate finance and eventually fractional CFO work has convinced me that most founders do not need more finance theory. They need one model they trust, and the confidence to change an assumption in front of an investor.",
                'credentials' => [
                    'Chartered Accountant, 18 years in practice',
                    'Fractional CFO to 20+ founder-led companies',
                    'Advised on raises totalling ₹300 crore',
                ],
            ],
            [
                'slug' => 'vikram-nair',
                'name' => 'Vikram Nair',
                'headline' => 'Senior data engineer, ex-Flipkart, 12 years building pipelines',
                'is_featured' => true,
                'experience_years' => 12,
                'bio' => "I have spent twelve years moving data from wherever it is produced to wherever someone needs to trust it — first at two e-commerce companies during their highest-growth years, then as a consultant to teams rebuilding pipelines that had outgrown their original design.\n\nMost data engineering teaching starts with syntax. I start with the failure modes: what happens when the source schema changes without warning, when a job runs twice, when yesterday's numbers do not match today's backfill. Everything here is written to survive contact with a real production system, not just a tutorial dataset.",
                'credentials' => [
                    'Built the order-events pipeline handling 40M+ daily records at Flipkart',
                    'Led the migration of three legacy warehouses onto Snowflake',
                    'Azure Data Engineer Associate (DP-203) certified',
                    'Independent consultant to data teams at four Series B+ startups',
                ],
            ],
            [
                'slug' => 'ananya-krishnan',
                'name' => 'Ananya Krishnan',
                'headline' => 'Analytics engineering lead, dbt and BI specialist',
                'experience_years' => 9,
                'bio' => "I sit at the point where raw pipelines become numbers an executive will actually act on — nine years split between warehouse modelling and the dashboards built on top of it.\n\nThe gap I keep finding is not technical skill, it is discipline: models nobody documented, dashboards nobody can trace back to a source table, metrics that quietly mean two different things in two different reports. I teach the workflow that closes that gap — tested models, documented sources, and a BI layer that traces cleanly back to where every number came from.",
                'credentials' => [
                    'Analytics engineering lead for a 200-person fintech data team',
                    'Migrated a 300+ model warehouse from stored procedures to dbt',
                    'Microsoft Certified: Power BI Data Analyst Associate',
                    'Built the executive reporting layer used in board decks at two companies',
                ],
            ],
        ];

        $instructors = [];

        foreach ($rows as $row) {
            $instructors[$row['slug']] = Instructor::updateOrCreate(['slug' => $row['slug']], $row);
        }

        return $instructors;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, Category>  $categories
     * @param  array<string, Instructor>  $instructors
     */
    private function seedCourse(array $definition, int $position, array $categories, array $instructors): void
    {
        $course = Course::updateOrCreate(
            ['slug' => $definition['slug']],
            [
                'category_id' => $categories[$definition['category']]->id,
                'instructor_id' => $instructors[$definition['instructor']]->id,
                'title' => $definition['title'],
                'short_description' => $definition['short'],
                'description' => $definition['description'],
                'price' => $definition['price'],
                'compare_at_price' => $definition['compare_at'] ?? null,
                'level' => $definition['level'],
                'language' => 'English',
                'status' => $definition['status'] ?? CourseStatus::Published,
                'is_featured' => $definition['featured'] ?? false,
                'sort_order' => $position + 1,
                'outcomes' => $definition['outcomes'],
                'requirements' => $definition['requirements'],
                'audience' => $definition['audience'],
                'certificate_enabled' => true,
                'completion_threshold' => 100,
                'thumbnail_path' => DemoThumbnailFactory::make(
                    $definition['slug'],
                    $definition['title'],
                    $definition['category'],
                    $definition['level']->value,
                ),
                'preview_video_id' => self::DEMO_VIDEO_IDS[0],
                'preview_video_url' => 'https://www.youtube.com/watch?v='.self::DEMO_VIDEO_IDS[0],
                'published_at' => ($definition['status'] ?? CourseStatus::Published) === CourseStatus::Published
                    ? now()->subDays(120 - ($position * 12))
                    : null,
            ],
        );

        $course->modules()->delete();
        $totalMinutes = 0;
        $lessonIndex = 0;

        foreach ($definition['modules'] as $moduleIndex => $moduleData) {
            $module = $course->modules()->create([
                'title' => $moduleData['title'],
                'description' => $moduleData['description'] ?? null,
                'sort_order' => $moduleIndex + 1,
            ]);

            foreach ($moduleData['lessons'] as $lessonPosition => $lessonData) {
                $type = $lessonData['type'] ?? LessonType::Video;
                $videoId = self::DEMO_VIDEO_IDS[$lessonIndex % count(self::DEMO_VIDEO_IDS)];

                $lesson = Lesson::create([
                    'course_id' => $course->id,
                    'course_module_id' => $module->id,
                    'title' => $lessonData['title'],
                    'slug' => Str::slug($lessonData['title']),
                    'type' => $type,
                    'description' => $lessonData['description'] ?? null,
                    'content' => $type === LessonType::Text ? ($lessonData['content'] ?? null) : null,
                    'video_url' => $type === LessonType::Video ? 'https://www.youtube.com/watch?v='.$videoId : null,
                    'video_id' => $type === LessonType::Video ? $videoId : null,
                    'duration_minutes' => $lessonData['minutes'],
                    'sort_order' => $lessonPosition + 1,
                    'is_preview' => $lessonData['preview'] ?? false,
                    'is_published' => true,
                ]);

                $totalMinutes += $lessonData['minutes'];
                $lessonIndex++;

                if (! empty($lessonData['resource'])) {
                    $this->attachResource($lesson, $lessonData['resource']);
                }
            }
        }

        $course->forceFill(['duration_minutes' => $totalMinutes])->save();

        $this->seedLiveClasses($course, $definition['live_classes'] ?? []);

        $course->faqs()->delete();

        foreach ($definition['faqs'] ?? [] as $index => $faq) {
            Faq::create([
                'course_id' => $course->id,
                'question' => $faq[0],
                'answer' => $faq[1],
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * @param  array{title: string, file: string, body: string}  $resource
     */
    private function attachResource(Lesson $lesson, array $resource): void
    {
        $path = DemoFileFactory::pdf(
            'lesson-resources/'.$lesson->id,
            Str::random(24).'.pdf',
            $resource['title'],
            $resource['body'],
        );

        $lesson->resources()->create([
            'title' => $resource['title'],
            'file_path' => $path,
            'original_name' => $resource['file'],
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'file_size' => (int) Storage::disk('local')->size($path),
            'sort_order' => 1,
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $definitions
     */
    private function seedLiveClasses(Course $course, array $definitions): void
    {
        foreach ($definitions as $index => $definition) {
            $isPast = $definition['past'] ?? false;

            LiveClass::updateOrCreate(
                ['slug' => Str::slug($definition['title'])],
                [
                    'course_id' => $course->id,
                    'instructor_id' => $course->instructor_id,
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'preparation_notes' => $definition['preparation'] ?? null,
                    'scheduled_at' => $isPast
                        ? now()->subDays(10 + $index * 7)->setTime(18, 30)
                        : now()->addDays(4 + $index * 7)->setTime(18, 30),
                    'duration_minutes' => $definition['minutes'] ?? 60,
                    'zoom_url' => 'https://us02web.zoom.us/j/'.random_int(10000000000, 99999999999),
                    'zoom_meeting_id' => (string) random_int(10000000000, 99999999999),
                    'status' => $isPast ? LiveClassStatus::Completed : LiveClassStatus::Scheduled,
                    'recording_url' => $isPast ? 'https://www.youtube.com/watch?v='.self::DEMO_VIDEO_IDS[0] : null,
                ],
            );
        }
    }

    private function seedGeneralFaqs(): void
    {
        $rows = [
            ['How long do I keep access to a course?', 'Enrolment does not expire unless we tell you otherwise at the point of sale. Recorded lessons, resources and live-class recordings stay in your account.'],
            ['Do I need to attend the live classes?', 'No. They are scheduled for questions and critique, and every session is recorded and attached to the course afterwards. Attending is better, but the curriculum stands on its own.'],
            ['What happens if I fall behind?', 'Nothing. There is no cohort clock. Your progress is saved per lesson and the dashboard always shows you the next thing to watch.'],
            ['Is there a certificate?', 'Yes. Complete every published lesson in a course and the certificate is issued automatically, with a public link an employer can use to verify it.'],
            ['Can I pay in instalments?', 'Write to us before enrolling and we will arrange it. Payment is handled by our team directly at the moment.'],
            ['Do you offer refunds?', 'If a course is not right for you, tell us within fourteen days of enrolling and we will refund it in full.'],
        ];

        Faq::query()->whereNull('course_id')->delete();

        foreach ($rows as $index => $row) {
            Faq::create([
                'question' => $row[0],
                'answer' => $row[1],
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);
        }
    }

    private function seedTestimonials(): void
    {
        $rows = [
            ['Meera Sundaram', 'Growth lead, D2C skincare', 5, true, 'I had been running ads for two years without a real testing framework. The third module alone changed how we plan a quarter — we cut two channels and put the money where it was already working.'],
            ['Jude Mensah', 'Founder, independent studio', 5, true, 'The identity module gave me language for decisions I was making on instinct. My client presentations got noticeably shorter and my revisions dropped.'],
            ['Rohit Bansal', 'Co-founder, logistics SaaS', 5, true, 'I built our first investor model during the course and used it in a raise six weeks later. Anita answered a question of mine in a live class that saved me a very embarrassing mistake.'],
            ['Fiona Clarke', 'Freelance copywriter', 4, false, 'Practical and unglamorous in the best way. No growth-hacking nonsense, just the actual sequence of work.'],
        ];

        Testimonial::query()->delete();

        foreach ($rows as $index => $row) {
            Testimonial::create([
                'name' => $row[0],
                'role' => $row[1],
                'rating' => $row[2],
                'is_featured' => $row[3],
                'quote' => $row[4],
                'is_published' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function courseDefinitions(): array
    {
        return require database_path('seeders/data/courses.php');
    }
}
