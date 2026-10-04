<?php

namespace Tests\Feature\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_refunding_a_razorpay_payment_calls_razorpays_refund_endpoint(): void
    {
        $settings = app(SettingsRepository::class);
        $settings->set('razorpay_enabled', '1', group: 'payments');
        $settings->set('razorpay_key_id', 'rzp_test_abc', group: 'payments');
        $settings->set('razorpay_key_secret', app(PaymentSettings::class)->encrypt('secret'), group: 'payments');

        Http::fake(['api.razorpay.com/*' => Http::response(['id' => 'rfnd_1'], 200)]);

        $order = Order::factory()->paid()->create();
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'gateway' => 'razorpay',
            'gateway_payment_id' => 'pay_test789',
            'status' => PaymentStatus::Successful,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.orders.refund', $order))
            ->assertRedirect();

        $this->assertSame(PaymentStatus::Refunded, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Refunded, $order->fresh()->status);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/payments/pay_test789/refund'));
    }

    public function test_a_failed_gateway_refund_call_leaves_the_payment_successful_and_reports_the_error(): void
    {
        $settings = app(SettingsRepository::class);
        $settings->set('razorpay_enabled', '1', group: 'payments');
        $settings->set('razorpay_key_id', 'rzp_test_abc', group: 'payments');
        $settings->set('razorpay_key_secret', app(PaymentSettings::class)->encrypt('secret'), group: 'payments');

        Http::fake(['api.razorpay.com/*' => Http::response(['error' => ['description' => 'already refunded']], 400)]);

        $order = Order::factory()->paid()->create();
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'gateway' => 'razorpay',
            'gateway_payment_id' => 'pay_test999',
            'status' => PaymentStatus::Successful,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.orders.refund', $order))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame(PaymentStatus::Successful, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
    }
}
