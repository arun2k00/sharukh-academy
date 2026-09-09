<?php

namespace Tests\Feature\Admin;

use App\Models\CertificateTemplate;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * A real, readable PNG — GD needs actual image bytes, not a random blob.
     */
    private function fakeArtwork(int $width = 2000, int $height = 1414): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'cert').'.png';
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 250, 250, 245));
        imagepng($image, $path);

        return new UploadedFile($path, 'artwork.png', 'image/png', null, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function fieldPayload(): array
    {
        $field = ['x' => 50, 'y' => 50, 'font' => 'serif', 'font_size' => 60, 'color' => '#1a1a18', 'align' => 'center'];

        return [
            'fields' => [
                'student_name' => $field,
                'course_name' => $field,
                'completion_date' => $field,
                'certificate_id' => $field,
            ],
        ];
    }

    public function test_an_administrator_uploads_a_certificate_template(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.certificate-templates.store'), [
            'name' => 'Gold border',
            'image' => $this->fakeArtwork(),
            'is_active' => '1',
            ...$this->fieldPayload(),
        ]);

        $template = CertificateTemplate::firstOrFail();
        $response->assertRedirect(route('admin.certificate-templates.edit', $template));

        $this->assertSame(2000, $template->image_width);
        $this->assertSame(1414, $template->image_height);
        Storage::disk('public')->assertExists($template->image_path);
    }

    public function test_a_student_cannot_manage_certificate_templates(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.certificate-templates.index'))
            ->assertForbidden();

        $this->actingAs($this->student())
            ->post(route('admin.certificate-templates.store'), [
                'name' => 'Sneaky',
                'image' => $this->fakeArtwork(),
                ...$this->fieldPayload(),
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('certificate_templates', 0);
    }

    public function test_optional_fields_are_only_saved_when_enabled(): void
    {
        $template = CertificateTemplate::factory()->create();

        $payload = $this->fieldPayload();
        $payload['fields']['instructor_name'] = ['enabled' => '0', 'x' => 10, 'y' => 10, 'font' => 'sans', 'font_size' => 20, 'color' => '#000000', 'align' => 'left'];
        $payload['fields']['academy_name'] = ['enabled' => '1', 'x' => 90, 'y' => 90, 'font' => 'sans', 'font_size' => 20, 'color' => '#000000', 'align' => 'right'];

        $this->actingAs($this->admin())->put(route('admin.certificate-templates.update', $template), [
            'name' => $template->name,
            'is_active' => '1',
            ...$payload,
        ]);

        $fields = $template->fresh()->fields;

        $this->assertArrayNotHasKey('instructor_name', $fields);
        $this->assertArrayHasKey('academy_name', $fields);
    }

    public function test_a_course_can_be_assigned_a_certificate_template(): void
    {
        $template = CertificateTemplate::factory()->create();
        $course = Course::factory()->create();

        $this->assertNull($course->certificateTemplate);

        $course->update(['certificate_template_id' => $template->id]);

        $this->assertTrue($course->fresh()->certificateTemplate->is($template));
    }

    public function test_deleting_a_template_still_in_use_is_refused(): void
    {
        $template = CertificateTemplate::factory()->create();
        Course::factory()->create(['certificate_template_id' => $template->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.certificate-templates.destroy', $template))
            ->assertRedirect();

        $this->assertDatabaseHas('certificate_templates', ['id' => $template->id]);
    }

    public function test_an_unused_template_can_be_deleted(): void
    {
        $template = CertificateTemplate::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.certificate-templates.destroy', $template))
            ->assertRedirect(route('admin.certificate-templates.index'));

        $this->assertDatabaseMissing('certificate_templates', ['id' => $template->id]);
    }

    public function test_the_admin_preview_renders_a_real_image_at_the_templates_resolution(): void
    {
        $template = CertificateTemplate::factory()->create();
        $this->writeRealPng($template);

        $response = $this->actingAs($this->admin())->get(route('admin.certificate-templates.preview', $template));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');

        $size = getimagesizefromstring($response->getContent());
        $this->assertSame((int) $template->image_width, $size[0]);
        $this->assertSame((int) $template->image_height, $size[1]);
    }

    public function test_a_student_cannot_load_the_admin_preview(): void
    {
        $template = CertificateTemplate::factory()->create();
        $this->writeRealPng($template);

        $this->actingAs($this->student())
            ->get(route('admin.certificate-templates.preview', $template))
            ->assertForbidden();
    }

    private function writeRealPng(CertificateTemplate $template): void
    {
        $image = imagecreatetruecolor($template->image_width, $template->image_height);
        imagefill($image, 0, 0, imagecolorallocate($image, 250, 250, 245));

        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();

        Storage::disk('public')->put($template->image_path, $bytes);
    }
}
