<?php

namespace App\Services\Payments;

use RuntimeException;

class PaymentGatewayUnavailable extends RuntimeException
{
    public static function unknownDriver(string $driver): self
    {
        return new self(
            "No payment gateway is registered for the [{$driver}] driver. Register an "
            .'implementation of '.PaymentGateway::class.' in PaymentManager, or set '
            .'PAYMENT_DRIVER=manual to fall back to administrator-confirmed enrollments.'
        );
    }

    public static function notConfigured(string $driver): self
    {
        return new self(
            "The [{$driver}] payment gateway is missing its credentials. Add them to the "
            .'environment file, or set PAYMENT_DRIVER=manual.'
        );
    }
}
