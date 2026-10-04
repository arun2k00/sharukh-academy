<?php

namespace Tests\Feature\Admin;

use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_saves_razorpay_credentials(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.payments.update'), [
                'razorpay_enabled' => '1',
                'razorpay_key_id' => 'rzp_test_abc123',
                'razorpay_key_secret' => 'super-secret-key',
                'razorpay_webhook_secret' => 'webhook-secret',
            ])
            ->assertRedirect(route('admin.settings.payments.edit'));

        $paymentSettings = app(PaymentSettings::class);

        $this->assertTrue($paymentSettings->razorpayEnabled());
        $this->assertSame('rzp_test_abc123', $paymentSettings->razorpayKeyId());
        $this->assertSame('super-secret-key', $paymentSettings->razorpayKeySecret());
        $this->assertSame('webhook-secret', $paymentSettings->razorpayWebhookSecret());

        // The secret never sits in the settings table in plain text.
        $stored = app(SettingsRepository::class)->get('razorpay_key_secret');
        $this->assertStringNotContainsString('super-secret-key', (string) $stored);
    }

    public function test_leaving_the_secret_field_blank_keeps_the_previously_saved_secret(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.payments.update'), [
            'razorpay_key_id' => 'rzp_test_abc123',
            'razorpay_key_secret' => 'original-secret',
        ]);

        $this->actingAs($this->admin())->put(route('admin.settings.payments.update'), [
            'razorpay_key_id' => 'rzp_test_abc123',
            'razorpay_key_secret' => '',
        ]);

        $this->assertSame('original-secret', app(PaymentSettings::class)->razorpayKeySecret());
    }

    public function test_a_non_admin_cannot_view_or_update_payment_settings(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.settings.payments.edit'))
            ->assertForbidden();

        $this->actingAs($this->student())
            ->put(route('admin.settings.payments.update'), ['razorpay_key_id' => 'x'])
            ->assertForbidden();
    }
}
