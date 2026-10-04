<?php

namespace App\Services\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay Payment Links — a hosted, redirect-based checkout page. No card
 * data ever touches this application, and the contract (get a URL, redirect
 * the buyer to it) matches {@see CheckoutSession::redirect()} with zero
 * controller or view changes needed beyond picking this driver.
 */
class RazorpayGateway implements PaymentGateway
{
    private const BASE_URL = 'https://api.razorpay.com/v1';

    public function __construct(
        private readonly PaymentSettings $settings,
    ) {}

    public function identifier(): string
    {
        return 'razorpay';
    }

    public function label(): string
    {
        return 'Razorpay';
    }

    public function isConfigured(): bool
    {
        return $this->settings->razorpayEnabled()
            && filled($this->settings->razorpayKeyId())
            && filled($this->settings->razorpayKeySecret());
    }

    public function createCheckout(Order $order): CheckoutSession
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->identifier(),
            'amount' => $order->amount,
            'currency' => $order->currency,
            'status' => PaymentStatus::Pending,
        ]);

        $response = Http::withBasicAuth($this->settings->razorpayKeyId(), $this->settings->razorpayKeySecret())
            ->timeout(15)
            ->post(self::BASE_URL.'/payment_links', [
                'amount' => Money::toSubunit((string) $order->amount),
                'currency' => $order->currency,
                'description' => "Enrollment: {$order->course->title}",
                'customer' => [
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'notify' => ['sms' => false, 'email' => true],
                'reminder_enable' => false,
                'notes' => [
                    'order_id' => (string) $order->id,
                    'order_number' => $order->order_number,
                    'course_id' => (string) $order->course_id,
                    'user_id' => (string) $order->user_id,
                    'payment_id' => (string) $payment->id,
                ],
                'callback_url' => route('payments.razorpay.callback', $order),
                'callback_method' => 'get',
            ]);

        if ($response->failed()) {
            Log::channel('payments')->error('Razorpay payment link creation failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $payment->update(['status' => PaymentStatus::Failed]);

            return CheckoutSession::manual(
                'We could not start Razorpay checkout just now. Our team will contact you at '
                .$order->user->email.' to arrange payment and open the course.'
            );
        }

        $body = $response->json();

        $payment->update([
            'gateway_order_id' => $body['id'] ?? null,
            'payload' => $body,
        ]);

        return CheckoutSession::redirect($body['short_url']);
    }

    public function refund(Payment $payment, ?int $amountInSmallestUnit = null): void
    {
        $response = Http::withBasicAuth($this->settings->razorpayKeyId(), $this->settings->razorpayKeySecret())
            ->timeout(15)
            ->post(self::BASE_URL."/payments/{$payment->gateway_payment_id}/refund", array_filter([
                'amount' => $amountInSmallestUnit,
            ]));

        if ($response->failed()) {
            Log::channel('payments')->error('Razorpay refund failed', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new PaymentGatewayUnavailable(
                "Razorpay refused the refund for payment #{$payment->id}: ".$response->body()
            );
        }

        $payment->update(['status' => PaymentStatus::Refunded]);
        $payment->order->update(['status' => OrderStatus::Refunded]);
    }
}
