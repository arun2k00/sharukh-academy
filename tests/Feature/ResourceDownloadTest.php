<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\LessonResource;
use App\Models\LiveClass;
use App\Models\LiveClassResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Attachments live outside the web root, so the only route to them runs
 * through a controller that checks enrolment first.
 */
class ResourceDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function lessonResource(): LessonResource
    {
        $course = $this->courseWithCurriculum();
        $lesson = $this->firstLesson($course);

        Storage::disk('local')->put('lesson-resources/test/worksheet.pdf', 'demo file contents');

        return LessonResource::factory()->create([
            'lesson_id' => $lesson->id,
            'file_path' => 'lesson-resources/test/worksheet.pdf',
            'original_name' => 'worksheet.pdf',
        ]);
    }

    public function test_an_enrolled_student_can_download_a_lesson_resource(): void
    {
        $resource = $this->lessonResource();
        $student = $this->student();

        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $resource->lesson->course_id,
        ]);

        $this->actingAs($student)
            ->get(route('resources.lesson', $resource))
            ->assertOk()
            ->assertDownload('worksheet.pdf');

        $this->assertSame(1, $resource->fresh()->downloads_count);
    }

    public function test_a_student_without_an_enrolment_is_refused(): void
    {
        $resource = $this->lessonResource();

        $this->actingAs($this->student())
            ->get(route('resources.lesson', $resource))
            ->assertForbidden();

        $this->assertSame(0, $resource->fresh()->downloads_count);
    }

    public function test_a_guest_is_sent_to_the_login_screen(): void
    {
        $resource = $this->lessonResource();

        $this->get(route('resources.lesson', $resource))->assertRedirect(route('login'));
    }

    public function test_a_missing_file_returns_a_not_found_rather_than_an_error(): void
    {
        $resource = $this->lessonResource();
        $student = $this->student();

        Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $resource->lesson->course_id,
        ]);

        Storage::disk('local')->delete($resource->file_path);

        $this->actingAs($student)
            ->get(route('resources.lesson', $resource))
            ->assertNotFound();
    }

    public function test_live_class_materials_follow_the_same_enrolment_rule(): void
    {
        $course = $this->courseWithCurriculum();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        Storage::disk('local')->put('live-class-resources/test/slides.pdf', 'demo slides');

        $resource = LiveClassResource::factory()->create([
            'live_class_id' => $liveClass->id,
            'file_path' => 'live-class-resources/test/slides.pdf',
            'original_name' => 'slides.pdf',
        ]);

        $outsider = $this->student();
        $this->actingAs($outsider)->get(route('resources.live-class', $resource))->assertForbidden();

        $enrolled = $this->student();
        Enrollment::factory()->create(['user_id' => $enrolled->id, 'course_id' => $course->id]);

        $this->actingAs($enrolled)
            ->get(route('resources.live-class', $resource))
            ->assertOk()
            ->assertDownload('slides.pdf');
    }
}
