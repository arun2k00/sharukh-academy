<?php

namespace App\Services\Payments;

use Illuminate\Contracts\Container\Container;

/**
 * Resolves the configured payment gateway.
 *
 * Only the manual gateway ships with the application. Real providers are
 * registered here once their credentials and SDK are available.
 */
class PaymentManager
{
    /**
     * Driver key => gateway implementation.
     *
     * @var array<string, class-string<PaymentGateway>>
     */
    private array $gateways = [
        'manual' => ManualGateway::class,
    ];

    public function __construct(
        private readonly Container $container,
    ) {}

    public function driver(?string $name = null): PaymentGateway
    {
        $name ??= (string) config('academy.payments.driver', 'manual');

        if (! isset($this->gateways[$name])) {
            throw PaymentGatewayUnavailable::unknownDriver($name);
        }

        $gateway = $this->container->make($this->gateways[$name]);

        if (! $gateway->isConfigured()) {
            throw PaymentGatewayUnavailable::notConfigured($name);
        }

        return $gateway;
    }

    /**
     * Whether checkout can currently take real money.
     */
    public function processesPaymentsOnline(): bool
    {
        return (string) config('academy.payments.driver', 'manual') !== 'manual';
    }

    /**
     * @param  class-string<PaymentGateway>  $gateway
     */
    public function extend(string $driver, string $gateway): void
    {
        $this->gateways[$driver] = $gateway;
    }
}
