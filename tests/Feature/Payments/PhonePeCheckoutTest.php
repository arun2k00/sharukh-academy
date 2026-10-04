<?php

namespace Tests\Feature\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhonePeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function enablePhonePe(): void
    {
        $settings = app(SettingsRepository::class);
        $settings->set('phonepe_enabled', '1', group: 'payments');
        $settings->set('phonepe_merchant_id', 'MERCHANTUAT', group: 'payments');
        $settings->set('phonepe_salt_key', app(PaymentSettings::class)->encrypt('salt-key-value'), group: 'payments');
        $settings->set('phonepe_salt_index', '1', group: 'payments');
    }

    public function test_starting_checkout_redirects_to_phonepes_hosted_page(): void
    {
        $this->enablePhonePe();

        Http::fake([
            'api-preprod.phonepe.com/*' => Http::response([
                'success' => true,
                'code' => 'PAYMENT_INITIATED',
                'data' => [
                    'instrumentResponse' => [
                        'redirectInfo' => ['url' => 'https://mercury.phonepe.com/transact/test'],
                    ],
                ],
            ], 200),
        ]);

        $course = $this->courseWithCurriculum(['price' => 4999]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll', $course), ['gateway' => 'phonepe'])
            ->assertRedirect('https://mercury.phonepe.com/transact/test');

        $payment = Payment::where('gateway', 'phonepe')->firstOrFail();
        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertNotNull($payment->gateway_order_id);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/pg/v1/pay')
            && $request->hasHeader('X-VERIFY'));
    }

    public function test_a_failed_checkout_call_falls_back_to_the_manual_message(): void
    {
        $this->enablePhonePe();

        Http::fake([
            'api-preprod.phonepe.com/*' => Http::response(['success' => false, 'code' => 'BAD_REQUEST'], 400),
        ]);

        $course = $this->courseWithCurriculum(['price' => 4999]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll', $course), ['gateway' => 'phonepe'])
            ->assertRedirect(route('courses.show', $course));

        $payment = Payment::where('gateway', 'phonepe')->firstOrFail();
        $this->assertSame(PaymentStatus::Failed, $payment->status);
    }
}
