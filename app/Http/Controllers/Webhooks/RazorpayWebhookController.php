<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\PaymentReconciler;
use App\Services\Payments\PaymentSettings;
use App\Support\Money;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Verifies the raw request body against Razorpay's HMAC signature before
 * trusting anything in it. Always returns 200 once the signature checks out
 * — even for an order we can't find or an event we don't act on — so
 * Razorpay does not retry a webhook that was received correctly.
 */
class RazorpayWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentSettings $settings, PaymentReconciler $reconciler): Response
    {
        $secret = $settings->razorpayWebhookSecret();

        if (blank($secret)) {
            Log::channel('payments')->error('Razorpay webhook received but no webhook secret is configured.');

            return response('Not configured', 503);
        }

        $signature = (string) $request->header('X-Razorpay-Signature');
        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expected, $signature)) {
            Log::channel('payments')->warning('Razorpay webhook signature mismatch.', [
                'ip' => $request->ip(),
            ]);

            return response('Invalid signature', 400);
        }

        $event = (string) $request->input('event');
        $paymentEntity = $request->input('payload.payment.entity', []);
        $notes = $paymentEntity['notes'] ?? $request->input('payload.payment_link.entity.notes', []);
        $paymentId = $notes['payment_id'] ?? null;

        if ($paymentId === null) {
            Log::channel('payments')->warning('Razorpay webhook missing our payment reference in notes.', [
                'event' => $event,
            ]);

            return response('OK', 200);
        }

        $payment = Payment::where('id', $paymentId)->where('gateway', 'razorpay')->first();

        if ($payment === null) {
            Log::channel('payments')->warning('Razorpay webhook references an unknown payment.', [
                'event' => $event,
                'payment_id' => $paymentId,
            ]);

            return response('OK', 200);
        }

        try {
            if (in_array($event, ['payment_link.paid', 'payment.captured'], true)) {
                $reconciler->markSuccessful(
                    $payment,
                    (string) ($paymentEntity['id'] ?? $payment->gateway_payment_id ?? ''),
                    Money::fromSubunit((int) ($paymentEntity['amount'] ?? 0)),
                    $request->all(),
                );
            } elseif (in_array($event, ['payment_link.cancelled', 'payment_link.expired', 'payment.failed'], true)) {
                $reconciler->markFailed($payment, $request->all());
            }
        } catch (UniqueConstraintViolationException $e) {
            // Two near-simultaneous deliveries for the same event; the first
            // one already reconciled this payment.
            Log::channel('payments')->info('Razorpay webhook duplicate delivery ignored.', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK', 200);
    }
}
