<?php

namespace Tests\Feature\Admin;

use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Brand settings (§23) take effect immediately on the public site with no
 * code changes — logo, favicon, and the two accent colours.
 */
class BrandSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_an_administrator_updates_the_academy_name_and_colors(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'academy_name' => 'Northwind Academy',
                'color_primary' => '#0044ff',
                'color_secondary' => '#222222',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'academy_name', 'value' => 'Northwind Academy']);
        $this->assertDatabaseHas('settings', ['key' => 'color_primary', 'value' => '#0044ff']);
    }

    public function test_a_custom_academy_name_appears_on_the_public_homepage(): void
    {
        app(SettingsRepository::class)->set('academy_name', 'Northwind Academy');

        $this->get(route('home'))->assertOk()->assertSee('Northwind Academy');
    }

    public function test_uploading_a_logo_replaces_the_drawn_wordmark(): void
    {
        $logo = UploadedFile::fake()->image('logo.png', 200, 60);

        $this->actingAs($this->admin())->put(route('admin.settings.update'), ['logo' => $logo]);

        $path = app(SettingsRepository::class)->get('logo_path');
        Storage::disk('public')->assertExists($path);

        $this->get(route('home'))->assertOk()->assertSee(Storage::disk('public')->url($path), false);
    }

    public function test_replacing_a_logo_deletes_the_previous_file(): void
    {
        $first = UploadedFile::fake()->image('first.png');
        $this->actingAs($this->admin())->put(route('admin.settings.update'), ['logo' => $first]);
        $firstPath = app(SettingsRepository::class)->get('logo_path');

        $second = UploadedFile::fake()->image('second.png');
        $this->actingAs($this->admin())->put(route('admin.settings.update'), ['logo' => $second]);

        Storage::disk('public')->assertMissing($firstPath);
    }

    public function test_a_student_cannot_update_brand_settings(): void
    {
        $this->actingAs($this->student())
            ->put(route('admin.settings.update'), ['academy_name' => 'Hijacked'])
            ->assertForbidden();
    }

    public function test_an_invalid_color_value_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), ['color_primary' => 'not-a-color'])
            ->assertSessionHasErrors('color_primary');
    }
}
