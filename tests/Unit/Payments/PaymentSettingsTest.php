<?php

namespace Tests\Unit\Payments;

use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_secret_is_encrypted_at_rest_and_decrypts_back_to_the_original(): void
    {
        $settings = app(SettingsRepository::class);
        $paymentSettings = app(PaymentSettings::class);

        $settings->set('razorpay_key_secret', $paymentSettings->encrypt('sk_test_super_secret'), group: 'payments');

        $stored = $settings->get('razorpay_key_secret');
        $this->assertNotSame('sk_test_super_secret', $stored);
        $this->assertStringNotContainsString('sk_test_super_secret', (string) $stored);

        $this->assertSame('sk_test_super_secret', $paymentSettings->razorpayKeySecret());
    }

    public function test_encrypting_a_blank_value_stores_nothing(): void
    {
        $paymentSettings = app(PaymentSettings::class);

        $this->assertNull($paymentSettings->encrypt(null));
        $this->assertNull($paymentSettings->encrypt(''));
    }

    public function test_razorpay_is_disabled_until_explicitly_turned_on(): void
    {
        $settings = app(SettingsRepository::class);
        $paymentSettings = app(PaymentSettings::class);

        $settings->set('razorpay_key_id', 'rzp_test_123', group: 'payments');
        $settings->set('razorpay_key_secret', $paymentSettings->encrypt('secret'), group: 'payments');

        $this->assertFalse($paymentSettings->razorpayEnabled());

        $settings->set('razorpay_enabled', '1', group: 'payments');

        $this->assertTrue($paymentSettings->razorpayEnabled());
    }
}
