<?php

namespace Tests\Feature;

use App\Notifications\AcademyNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandedEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_academy_notification_renders_the_branded_template(): void
    {
        $user = $this->student();

        $notification = new AcademyNotification(
            kind: 'welcome',
            title: 'Welcome',
            body: 'Your account is ready.',
            url: 'https://example.test/courses',
            mailTemplateKey: 'welcome',
            mailVariables: ['student_name' => $user->name, 'academy_name' => config('academy.name')],
        );

        $rendered = $notification->toMail($user)->render();

        $this->assertStringContainsString(config('academy.name'), $rendered);
        $this->assertStringContainsString('https://example.test/courses', $rendered);
        $this->assertStringContainsString(config('academy.support_email'), $rendered);
    }

    public function test_the_password_reset_email_uses_the_branded_template(): void
    {
        $user = $this->student();

        $rendered = (new ResetPassword('a-fake-token'))->toMail($user)->render();

        $this->assertStringContainsString('Reset password', $rendered);
        $this->assertStringContainsString('reset-password/a-fake-token', $rendered);
    }

    public function test_the_verify_email_notification_uses_the_branded_template(): void
    {
        $user = $this->student();

        $rendered = (new VerifyEmail)->toMail($user)->render();

        $this->assertStringContainsString('Verify email address', $rendered);
    }
}
