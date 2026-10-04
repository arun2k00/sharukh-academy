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
use Database\Seeders\Support\DemoAvatarFactory;
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
                'slug' => 'sharuk-shaik',
                'name' => 'Sharuk Shaik',
                'headline' => 'Enterprise data engineering trainer with 10+ years of experience and 3000+ students trained',
                'is_featured' => true,
                'experience_years' => 10,
                'bio' => "Sharukh Sir is a seasoned data engineering professional with over a decade of experience building and managing enterprise data platforms across multiple industries including banking, insurance, retail, and healthcare.\n\nHaving worked with organizations ranging from fast-growing startups to Fortune 500 companies, he brings a unique perspective that blends deep technical knowledge with practical business understanding.\n\nHis teaching philosophy is simple: theory without practice is incomplete. Every concept is taught with real-world context, every module ends with hands-on exercises, and every student graduates with production-grade project experience.\n\nHis mission is to make every student industry-ready from day one — not just in tools and technology, but in mindset, problem-solving, and professional communication.",
                'credentials' => [
                    '10+ Years Industry Experience',
                    '3000+ Students Trained',
                    'Enterprise-Grade Expertise',
                    'Active Mentorship Community',
                    'Hands-on expertise across Snowflake, dbt, Azure Data Factory, Power BI, SQL Server, Python, Azure DevOps and Git',
                ],
            ],
            [
                'slug' => 'arun-singh',
                'name' => 'Arun Singh',
                'headline' => 'Full-stack engineer & cloud architect — 10+ years, 1000+ students trained',
                'experience_years' => 10,
                'bio' => "I have spent over a decade building software end to end — from the backend and the database schema underneath it, through the frontend a user actually touches, to the server it all runs on in production.\n\nMy background spans building products from scratch for early-stage startups and hardening infrastructure for larger, more established teams — writing the code, deploying it correctly on both Linux and Windows servers, and designing the cloud architecture that keeps it running once real users show up.\n\nI teach the way I build: nothing is finished until it is deployed, monitored, and left in a state another engineer can maintain without having to call you at 11pm.",
                'credentials' => [
                    '10+ Years Industry Experience',
                    '1000+ Students Trained',
                    'Enterprise-Grade Expertise',
                    'Full-stack development across frontend, backend and databases, with complete deployment skills on Linux and Windows servers, and cloud architecture experience',
                ],
            ],
            [
                'slug' => 'vikram-nair',
                'name' => 'Vikram Nair',
                'headline' => 'Senior data engineer, ex-Flipkart, 12 years building pipelines',
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

        foreach ($rows as $index => $row) {
            $row['avatar_path'] = DemoAvatarFactory::make($row['slug'], $row['name'], $index);
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
                // Thumbnail: a generated cover distinct to this course where
                // one exists, the shared stock photo otherwise (swap for real
                // per-course photography from the admin course form).
                'thumbnail_path' => DemoThumbnailFactory::coursePhotoPath($definition['slug'])
                    ?? DemoThumbnailFactory::stockPhotoPath()
                    ?? DemoThumbnailFactory::make(
                        $definition['slug'],
                        $definition['title'],
                        $definition['category'],
                        $definition['level']->value,
                    ),
                // Banner: the same fixed header image on every course page —
                // see public/bannerheader.jpg.
                'banner_path' => DemoThumbnailFactory::stockPhotoPath() ?? DemoThumbnailFactory::makeBanner(
                    $definition['slug'],
                    $definition['title'],
                    $definition['category'],
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
            ['How long do I keep access to a course?', 'Enrollment does not expire unless we tell you otherwise at the point of sale. Recorded lessons, resources and live-class recordings stay in your account.'],
            ['Do I need to attend the live classes?', 'No. They are scheduled for questions and critique, and every session is recorded and attached to the course afterwards. Attending is better, but the curriculum stands on its own.'],
            ['What happens if I fall behind?', 'Nothing. There is no cohort clock. Your progress is saved per lesson and the dashboard always shows you the next thing to watch.'],
            ['Is there a certificate?', 'Yes. Complete every published lesson in a course and the certificate is issued automatically, with a public link an employer can use to verify it.'],
            ['Can I pay in instalments?', 'Write to us before enrolling and we will arrange it. Payment is handled by our team directly at the moment.'],
            ['Do you offer refunds?', 'No. Every course is a digital product you get full access to the moment you enroll, so we do not offer refunds for a change of mind — please review the curriculum and free preview lessons carefully before enrolling. See our Refund Policy for the limited exceptions (like a duplicate charge) that we do correct.'],
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
            ['Meera Sundaram', 'Data Analyst, D2C retail', 5, true, 'The SQL course alone was worth it — window functions and query optimisation are things my last job never actually taught me. I went into my next interview and answered the SQL round without hesitating once.'],
            ['Jude Mensah', 'BI Developer, fintech startup', 5, true, 'Sharuk breaks down Snowflake in a way that finally made warehouse architecture click. The real-world project at the end is genuinely production-grade, not a toy exercise.'],
            ['Rohit Bansal', 'Data Engineer, logistics SaaS', 5, true, 'I built our first Azure Data Factory pipeline during the course and had it running in production three weeks later. The monitoring and error-handling module alone saved us from a bad incident.'],
            ['Fiona Clarke', 'Analytics Engineer, healthcare tech', 4, false, 'Practical and unglamorous in the best way — no theory for its own sake. The dbt course is exactly the workflow our team uses now, tests and all.'],
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
