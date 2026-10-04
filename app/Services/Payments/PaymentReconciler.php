<?php

namespace App\Services\Payments;

use App\Enums\EnrollmentSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\EnrollmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * The single place that mutates Order/Payment/Enrollment state once a
 * gateway reports money has actually moved. Both webhook controllers only
 * verify their own signature and payload shape, then call this — so there is
 * exactly one code path that can ever grant paid access.
 */
class PaymentReconciler
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function markSuccessful(
        Payment $payment,
        string $gatewayPaymentId,
        float|string $reportedAmount,
        array $payload,
    ): Payment {
        return DB::transaction(function () use ($payment, $gatewayPaymentId, $reportedAmount, $payload): Payment {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->isSuccessful()) {
                return $payment;
            }

            if (bccomp((string) $reportedAmount, (string) $payment->amount, 2) !== 0) {
                Log::channel('payments')->critical('Payment amount mismatch — refusing to mark paid', [
                    'payment_id' => $payment->id,
                    'expected' => (string) $payment->amount,
                    'reported' => (string) $reportedAmount,
                ]);

                return $payment;
            }

            $payment->update([
                'gateway_payment_id' => $gatewayPaymentId,
                'status' => PaymentStatus::Successful,
                'payload' => $payload,
                'paid_at' => now(),
            ]);

            $order = $payment->order;
            $order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);

            $this->enrollments->enroll($order->user, $order->course, EnrollmentSource::Purchase, $order);

            return $payment;
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function markFailed(Payment $payment, array $payload): Payment
    {
        $payment->update([
            'status' => PaymentStatus::Failed,
            'payload' => $payload,
        ]);

        return $payment;
    }
}
