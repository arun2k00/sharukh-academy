<?php

namespace Tests\Unit;

use App\Support\YouTube;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class YouTubeTest extends TestCase
{
    #[DataProvider('acceptedUrls')]
    public function test_it_extracts_the_video_id_from_accepted_urls(string $input, string $expected): void
    {
        $this->assertSame($expected, YouTube::id($input));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function acceptedUrls(): array
    {
        return [
            'watch url' => ['https://www.youtube.com/watch?v=M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'watch url with extra params' => ['https://www.youtube.com/watch?v=M7lc1UVf-VE&t=90s', 'M7lc1UVf-VE'],
            'short share url' => ['https://youtu.be/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'embed url' => ['https://www.youtube.com/embed/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'shorts url' => ['https://www.youtube.com/shorts/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'nocookie embed' => ['https://www.youtube-nocookie.com/embed/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'missing scheme' => ['youtu.be/M7lc1UVf-VE', 'M7lc1UVf-VE'],
            'bare id' => ['M7lc1UVf-VE', 'M7lc1UVf-VE'],
        ];
    }

    #[DataProvider('rejectedUrls')]
    public function test_it_rejects_anything_that_is_not_a_youtube_video(?string $input): void
    {
        $this->assertNull(YouTube::id($input));
        $this->assertFalse(YouTube::isValid($input));
    }

    /**
     * @return array<string, array{0: string|null}>
     */
    public static function rejectedUrls(): array
    {
        return [
            'null' => [null],
            'empty' => ['   '],
            'other host' => ['https://vimeo.com/123456789'],
            'lookalike host' => ['https://youtube.com.evil.test/watch?v=M7lc1UVf-VE'],
            'javascript url' => ['javascript:alert(1)'],
            'short id' => ['https://www.youtube.com/watch?v=abc'],
            'html injection' => ['"><script>alert(1)</script>'],
        ];
    }

    public function test_the_embed_url_uses_the_privacy_preserving_host(): void
    {
        $embed = YouTube::embedUrl('M7lc1UVf-VE');

        $this->assertNotNull($embed);
        $this->assertStringStartsWith('https://www.youtube-nocookie.com/embed/M7lc1UVf-VE?', $embed);
        $this->assertStringContainsString('rel=0', $embed);
    }

    public function test_it_will_not_build_an_embed_url_for_an_invalid_id(): void
    {
        $this->assertNull(YouTube::embedUrl('not-an-id'));
        $this->assertNull(YouTube::embedUrl(null));
        $this->assertNull(YouTube::thumbnailUrl('"><script>'));
    }
}
