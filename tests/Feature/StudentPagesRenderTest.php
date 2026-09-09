<?php

namespace Tests\Feature;

use App\Enums\LessonType;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_student_screen_renders(): void
    {
        $student = $this->student();
        $course = $this->courseWithCurriculum();
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);
        $lesson = $this->firstLesson($course);

        foreach ([
            route('dashboard'),
            route('my-courses'),
            route('learn.lesson', [$course, $lesson]),
            route('live-classes.index'),
            route('live-classes.show', $liveClass),
            route('certificates.index'),
            route('profile.edit'),
        ] as $url) {
            $this->actingAs($student)->get($url)->assertOk("Expected {$url} to render.");
        }
    }

    public function test_the_dashboard_renders_for_a_student_with_no_enrolments(): void
    {
        $this->actingAs($this->student())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('You are not enrolled in anything yet');
    }

    public function test_every_lesson_type_renders_in_the_learning_interface(): void
    {
        $student = $this->student();
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        $module = $course->modules()->firstOrFail();
        $liveClass = LiveClass::factory()->create(['course_id' => $course->id]);

        $text = Lesson::factory()->text()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
            'sort_order' => 2,
        ]);

        $live = Lesson::factory()->create([
            'course_id' => $course->id,
            'course_module_id' => $module->id,
            'type' => LessonType::Live,
            'live_class_id' => $liveClass->id,
            'video_url' => null,
            'video_id' => null,
            'sort_order' => 3,
        ]);

        foreach ([$this->firstLesson($course), $text, $live] as $lesson) {
            $this->actingAs($student)
                ->get(route('learn.lesson', [$course, $lesson]))
                ->assertOk("Expected the {$lesson->type->value} lesson to render.");
        }
    }

    public function test_the_certificate_screens_render(): void
    {
        $student = $this->student();
        $course = $this->courseWithCurriculum(modules: 1, lessonsPerModule: 1);
        Enrollment::factory()->create(['user_id' => $student->id, 'course_id' => $course->id]);

        app(ProgressService::class)->markComplete($student, $this->firstLesson($course));

        $certificate = Certificate::firstOrFail();

        $this->actingAs($student)->get(route('certificates.index'))->assertOk();
        $this->actingAs($student)->get(route('certificates.show', $certificate))->assertOk();
        $this->actingAs($student)->get(route('certificates.print', $certificate))->assertOk();
    }

    public function test_a_student_updates_their_profile(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->patch(route('profile.update'), [
                'name' => 'Meera Sundaram',
                'phone' => '+91 98765 43210',
                'bio' => 'Growth lead at a D2C brand.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('Meera Sundaram', $student->fresh()->name);
    }

    public function test_a_student_changes_their_password_with_the_current_one(): void
    {
        $student = $this->student();
        $student->forceFill(['password' => 'the-old-password'])->save();

        $this->actingAs($student)
            ->put(route('password.update'), [
                'current_password' => 'the-old-password',
                'password' => 'the-new-password',
                'password_confirmation' => 'the-new-password',
            ])
            ->assertSessionHas('status');

        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'the-new-password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_changing_a_password_without_the_current_one_fails(): void
    {
        $student = $this->student();
        $student->forceFill(['password' => 'the-old-password'])->save();

        $this->actingAs($student)
            ->put(route('password.update'), [
                'current_password' => 'the-wrong-password',
                'password' => 'the-new-password',
                'password_confirmation' => 'the-new-password',
            ])
            ->assertSessionHasErrors('current_password', errorBag: 'updatePassword');
    }
}
