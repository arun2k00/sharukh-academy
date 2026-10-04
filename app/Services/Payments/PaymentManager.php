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
     * Whether checkout can currently take real money through at least one
     * configured online gateway.
     */
    public function processesPaymentsOnline(): bool
    {
        return $this->availableOnlineDrivers() !== [];
    }

    /**
     * Every registered non-manual gateway whose credentials are actually
     * present — what the enrollment panel loops over to render one "Pay via
     * X" button per gateway an administrator has switched on.
     *
     * @return list<PaymentGateway>
     */
    public function availableOnlineDrivers(): array
    {
        $drivers = [];

        foreach (array_keys($this->gateways) as $name) {
            if ($name === 'manual') {
                continue;
            }

            $gateway = $this->container->make($this->gateways[$name]);

            if ($gateway->isConfigured()) {
                $drivers[] = $gateway;
            }
        }

        return $drivers;
    }

    /**
     * @param  class-string<PaymentGateway>  $gateway
     */
    public function extend(string $driver, string $gateway): void
    {
        $this->gateways[$driver] = $gateway;
    }
}
