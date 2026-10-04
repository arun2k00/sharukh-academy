<?php

namespace Tests\Feature\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RazorpayCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function enableRazorpay(): void
    {
        $settings = app(SettingsRepository::class);
        $settings->set('razorpay_enabled', '1', group: 'payments');
        $settings->set('razorpay_key_id', 'rzp_test_abc123', group: 'payments');
        $settings->set('razorpay_key_secret', app(PaymentSettings::class)->encrypt('secret'), group: 'payments');
    }

    public function test_starting_checkout_creates_a_payment_link_and_redirects_to_it(): void
    {
        $this->enableRazorpay();

        Http::fake([
            'api.razorpay.com/*' => Http::response([
                'id' => 'plink_test123',
                'short_url' => 'https://rzp.io/i/test123',
            ], 200),
        ]);

        $course = $this->courseWithCurriculum(['price' => 7999]);
        $student = $this->student();

        $this->actingAs($student)
            ->post(route('courses.enroll', $course), ['gateway' => 'razorpay'])
            ->assertRedirect('https://rzp.io/i/test123');

        $payment = Payment::where('gateway', 'razorpay')->firstOrFail();
        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame('plink_test123', $payment->gateway_order_id);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/payment_links')
            && $request['amount'] === 799900);
    }

    public function test_a_failed_checkout_call_falls_back_to_the_manual_message_without_a_500(): void
    {
        $this->enableRazorpay();

        Http::fake([
            'api.razorpay.com/*' => Http::response(['error' => ['description' => 'bad request']], 400),
        ]);

        $course = $this->courseWithCurriculum(['price' => 7999]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll', $course), ['gateway' => 'razorpay'])
            ->assertRedirect(route('courses.show', $course))
            ->assertSessionHas('status');

        $payment = Payment::where('gateway', 'razorpay')->firstOrFail();
        $this->assertSame(PaymentStatus::Failed, $payment->status);
    }

    public function test_checkout_falls_back_to_manual_when_razorpay_is_not_configured(): void
    {
        $course = $this->courseWithCurriculum(['price' => 7999]);

        $this->actingAs($this->student())
            ->post(route('courses.enroll', $course), ['gateway' => 'razorpay'])
            ->assertRedirect(route('courses.show', $course));

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('orders', ['course_id' => $course->id, 'status' => 'pending']);
    }
}
