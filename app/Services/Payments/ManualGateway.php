<?php

namespace App\Services\Payments;

use App\Models\Order;

/**
 * The default gateway: it processes no money at all.
 *
 * Paid courses record a pending order and tell the student that the academy
 * will be in touch. An administrator then confirms the enrolment by hand. This
 * is deliberately not a simulated payment — nothing is ever marked paid here.
 */
class ManualGateway implements PaymentGateway
{
    public function identifier(): string
    {
        return 'manual';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function createCheckout(Order $order): CheckoutSession
    {
        return CheckoutSession::manual(
            'Your enrolment request has been received. Our team will contact you at '
            .$order->user->email.' to arrange payment and open the course.'
        );
    }
}
