<?php

namespace App\Services\Payments;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;

/**
 * The default gateway: it processes no money at all.
 *
 * Paid courses record a pending order and tell the student that the academy
 * will be in touch. An administrator then confirms the enrollment by hand. This
 * is deliberately not a simulated payment — nothing is ever marked paid here.
 */
class ManualGateway implements PaymentGateway
{
    public function identifier(): string
    {
        return 'manual';
    }

    public function label(): string
    {
        return 'Manual';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function createCheckout(Order $order): CheckoutSession
    {
        return CheckoutSession::manual(
            'Your enrollment request has been received. Our team will contact you at '
            .$order->user->email.' to arrange payment and open the course.'
        );
    }

    /**
     * A manual refund is just the bookkeeping — no gateway to call.
     */
    public function refund(Payment $payment, ?int $amountInSmallestUnit = null): void
    {
        $payment->update(['status' => PaymentStatus::Refunded]);
        $payment->order->update(['status' => OrderStatus::Refunded]);
    }
}
