<?php

namespace App\Services\Payments;

/**
 * The result of starting checkout for an order.
 *
 * A gateway either hands back a URL to redirect the buyer to, or reports that
 * the order needs a human to confirm it. No implementation grants course
 * access by itself — that only happens once a payment is recorded successful
 * or an administrator enrols the student.
 */
final readonly class CheckoutSession
{
    public function __construct(
        public bool $requiresManualApproval,
        public string $message,
        public ?string $redirectUrl = null,
    ) {}

    public static function manual(string $message): self
    {
        return new self(requiresManualApproval: true, message: $message);
    }

    public static function redirect(string $url, string $message = ''): self
    {
        return new self(requiresManualApproval: false, message: $message, redirectUrl: $url);
    }
}
