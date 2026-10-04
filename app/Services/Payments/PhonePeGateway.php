<?php

namespace App\Services\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PhonePe Standard Checkout — redirect-based like Razorpay Payment Links, so
 * it fits the same {@see CheckoutSession::redirect()} contract. Every request
 * is a base64-encoded JSON payload authenticated by an X-VERIFY checksum
 * (SHA-256 of payload + path + salt key, plus the salt index) rather than
 * bearer auth.
 */
class PhonePeGateway implements PaymentGateway
{
    public function __construct(
        private readonly PaymentSettings $settings,
    ) {}

    public function identifier(): string
    {
        return 'phonepe';
    }

    public function label(): string
    {
        return 'PhonePe';
    }

    public function isConfigured(): bool
    {
        return $this->settings->phonePeEnabled()
            && filled($this->settings->phonePeMerchantId())
            && filled($this->settings->phonePeSaltKey());
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

        $merchantTransactionId = "{$order->order_number}-{$payment->id}";

        $payload = base64_encode(json_encode([
            'merchantId' => $this->settings->phonePeMerchantId(),
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => (string) $order->user_id,
            'amount' => Money::toSubunit((string) $order->amount),
            'redirectUrl' => route('payments.phonepe.callback', $order),
            'redirectMode' => 'REDIRECT',
            'callbackUrl' => route('webhooks.phonepe'),
            'paymentInstrument' => ['type' => 'PAY_PAGE'],
        ], JSON_THROW_ON_ERROR));

        $response = Http::withHeaders([
            'X-VERIFY' => $this->checksum($payload, '/pg/v1/pay'),
        ])
            ->timeout(15)
            ->post("{$this->baseUrl()}/pg/v1/pay", ['request' => $payload]);

        if ($response->failed() || $response->json('success') !== true) {
            Log::channel('payments')->error('PhonePe payment initiation failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $payment->update(['status' => PaymentStatus::Failed]);

            return CheckoutSession::manual(
                'We could not start PhonePe checkout just now. Our team will contact you at '
                .$order->user->email.' to arrange payment and open the course.'
            );
        }

        $body = $response->json();

        $payment->update([
            'gateway_order_id' => $merchantTransactionId,
            'payload' => $body,
        ]);

        return CheckoutSession::redirect($body['data']['instrumentResponse']['redirectInfo']['url']);
    }

    /**
     * PhonePe's own documented best practice: never trust a redirect or
     * webhook body alone — confirm the transaction's real status directly
     * before granting anything.
     */
    public function verifyStatus(string $merchantTransactionId): ?string
    {
        $path = '/pg/v1/status/'.$this->settings->phonePeMerchantId()."/{$merchantTransactionId}";

        $response = Http::withHeaders([
            'X-VERIFY' => $this->checksum('', $path),
            'X-MERCHANT-ID' => $this->settings->phonePeMerchantId(),
        ])->timeout(15)->get("{$this->baseUrl()}{$path}");

        if ($response->failed()) {
            return null;
        }

        return $response->json('code');
    }

    public function refund(Payment $payment, ?int $amountInSmallestUnit = null): void
    {
        $refundTransactionId = 'RF-'.Str::upper(Str::random(10));

        $payload = base64_encode(json_encode([
            'merchantId' => $this->settings->phonePeMerchantId(),
            'merchantUserId' => (string) $payment->order->user_id,
            'originalTransactionId' => $payment->gateway_order_id,
            'merchantTransactionId' => $refundTransactionId,
            'amount' => $amountInSmallestUnit ?? Money::toSubunit((string) $payment->amount),
            'callbackUrl' => route('webhooks.phonepe'),
        ], JSON_THROW_ON_ERROR));

        $response = Http::withHeaders([
            'X-VERIFY' => $this->checksum($payload, '/pg/v1/refund'),
        ])->timeout(15)->post("{$this->baseUrl()}/pg/v1/refund", ['request' => $payload]);

        if ($response->failed() || $response->json('success') !== true) {
            Log::channel('payments')->error('PhonePe refund failed', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new PaymentGatewayUnavailable(
                "PhonePe refused the refund for payment #{$payment->id}: ".$response->body()
            );
        }

        // PhonePe refunds resolve asynchronously via webhook; this only
        // records that one was requested, not that it settled.
        $payment->update(['status' => PaymentStatus::Refunded]);
        $payment->order->update(['status' => OrderStatus::Refunded]);
    }

    private function checksum(string $base64Payload, string $path): string
    {
        $hash = hash('sha256', $base64Payload.$path.$this->settings->phonePeSaltKey());

        return "{$hash}###{$this->settings->phonePeSaltIndex()}";
    }

    private function baseUrl(): string
    {
        return $this->settings->phonePeEnvironment() === 'production'
            ? 'https://api.phonepe.com/apis/hermes'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
    }
}
