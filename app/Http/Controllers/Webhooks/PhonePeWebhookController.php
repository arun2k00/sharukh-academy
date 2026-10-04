<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\PaymentReconciler;
use App\Services\Payments\PaymentSettings;
use App\Services\Payments\PhonePeGateway;
use App\Support\Money;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Verifies the X-VERIFY checksum, then — per PhonePe's own documented best
 * practice — calls the status API to confirm the transaction directly rather
 * than trusting the callback body alone, before reconciling anything.
 */
class PhonePeWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        PaymentSettings $settings,
        PhonePeGateway $gateway,
        PaymentReconciler $reconciler,
    ): Response {
        $base64Body = (string) $request->input('response');
        $signature = (string) $request->header('X-VERIFY');
        $saltKey = $settings->phonePeSaltKey();

        if (blank($saltKey) || blank($base64Body)) {
            Log::channel('payments')->error('PhonePe webhook received but is not configured or missing a body.');

            return response('Not configured', 503);
        }

        $expected = hash('sha256', $base64Body.$saltKey).'###'.$settings->phonePeSaltIndex();

        if (! hash_equals($expected, $signature)) {
            Log::channel('payments')->warning('PhonePe webhook checksum mismatch.', ['ip' => $request->ip()]);

            return response('Invalid signature', 400);
        }

        $decoded = json_decode(base64_decode($base64Body, true) ?: '{}', true) ?? [];
        $merchantTransactionId = $decoded['data']['merchantTransactionId'] ?? null;

        if ($merchantTransactionId === null) {
            return response('OK', 200);
        }

        $payment = Payment::where('gateway_order_id', $merchantTransactionId)->where('gateway', 'phonepe')->first();

        if ($payment === null) {
            Log::channel('payments')->warning('PhonePe webhook references an unknown payment.', [
                'merchant_transaction_id' => $merchantTransactionId,
            ]);

            return response('OK', 200);
        }

        // Never trust the callback body alone — confirm with PhonePe directly.
        $confirmedCode = $gateway->verifyStatus($merchantTransactionId);

        try {
            if ($confirmedCode === 'PAYMENT_SUCCESS') {
                $reconciler->markSuccessful(
                    $payment,
                    $decoded['data']['transactionId'] ?? $merchantTransactionId,
                    Money::fromSubunit((int) ($decoded['data']['amount'] ?? 0)),
                    $decoded,
                );
            } elseif (in_array($confirmedCode, ['PAYMENT_ERROR', 'PAYMENT_DECLINED'], true)) {
                $reconciler->markFailed($payment, $decoded);
            }
        } catch (UniqueConstraintViolationException $e) {
            Log::channel('payments')->info('PhonePe webhook duplicate delivery ignored.', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK', 200);
    }
}
