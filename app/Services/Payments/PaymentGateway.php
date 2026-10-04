<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;

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
     * A short label for buttons and admin screens (e.g. "Razorpay").
     */
    public function label(): string;

    /**
     * Whether the credentials this gateway needs are actually present.
     */
    public function isConfigured(): bool;

    /**
     * Begin checkout for a pending order.
     */
    public function createCheckout(Order $order): CheckoutSession;

    /**
     * Refund a successful payment, in full or for a specific smaller amount
     * (in the currency's smallest unit — paise for INR).
     */
    public function refund(Payment $payment, ?int $amountInSmallestUnit = null): void;
}
