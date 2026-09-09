<?php

namespace App\Services\Payments;

use App\Models\Order;

/**
 * Contract every payment provider implements.
 *
 * Adding Razorpay or Stripe later means writing one class against this
 * interface and pointing `academy.payments.driver` at it — no controller,
 * route or view has to change.
 */
interface PaymentGateway
{
    /**
     * Short driver key, matching the `academy.payments.driver` config value.
     */
    public function identifier(): string;

    /**
     * Whether the credentials this gateway needs are actually present.
     */
    public function isConfigured(): bool;

    /**
     * Begin checkout for a pending order.
     */
    public function createCheckout(Order $order): CheckoutSession;
}
