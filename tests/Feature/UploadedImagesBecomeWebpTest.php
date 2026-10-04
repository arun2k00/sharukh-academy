<?php

namespace Tests\Feature;

use App\Support\WebpImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadedImagesBecomeWebpTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_png_upload_is_stored_as_webp(): void
    {
        Storage::fake('public');

        $path = WebpImage::store(UploadedFile::fake()->image('avatar.png', 300, 300), 'avatars');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        $bytes = Storage::disk('public')->get($path);
        $this->assertSame('WEBP', substr($bytes, 8, 4), 'stored file should carry a WebP header');
    }

    public function test_an_oversized_upload_is_scaled_down(): void
    {
        Storage::fake('public');

        $path = WebpImage::store(UploadedFile::fake()->image('huge.jpg', 5000, 3000), 'covers');

        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($path));

        $this->assertSame(2400, $width);
        $this->assertSame(1440, $height);
    }

    public function test_a_file_gd_cannot_read_is_still_stored(): void
    {
        Storage::fake('public');

        $path = WebpImage::store(UploadedFile::fake()->create('notreally.png', 8), 'avatars');

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsNotWith('.webp', $path);
    }
}
