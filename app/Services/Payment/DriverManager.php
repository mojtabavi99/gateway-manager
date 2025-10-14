<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use App\Services\Payment\Contracts\PaymentDriver;
use App\Services\Payment\Drivers\ParsianDriver;
use App\Services\Payment\Drivers\SadadDriver;
use App\Services\Payment\Drivers\SamanDriver;
use App\Services\Payment\Drivers\ZarinpalDriver;
use InvalidArgumentException;

class DriverManager
{
    /**
     * Array of instantiated payment drivers
     *
     * @var PaymentDriver[]
     */
    protected array $drivers = [];

    /**
     * Returns an instance of the requested payment driver.
     *
     * @param string $name Driver name (e.g., 'saman', 'parsian', 'sadad')
     * @return PaymentDriver
     * @throws InvalidArgumentException
     */
    public function driver(string $name): PaymentDriver
    {
        return $this->getDriverInstance($name);
    }

    /**
     * Creates or returns a cached instance of the requested driver.
     *
     * @param string $slug Driver slug from database
     * @return PaymentDriver
     * @throws InvalidArgumentException
     */
    protected function getDriverInstance(string $slug): PaymentDriver
    {
        if (!isset($this->drivers[$slug])) {
            $gateway = PaymentGateway::query()
                ->where('slug', $slug)
                ->where('status', PaymentGateway::STATUS_ACTIVE)
                ->firstOrFail();

            if (!$gateway) {
                throw new InvalidArgumentException("Payment gateway not found: {$slug}");
            }

            $this->drivers[$slug] = match ($slug) {
                'saman' => new SamanDriver($gateway->merchant_code),
                'parsian' => new ParsianDriver($gateway->merchant_code),
                'sadad' => new SadadDriver($gateway->secret_key, $gateway->terminal_id, $gateway->merchant_code),
                'zarinpal' => new ZarinpalDriver($gateway->merchant_code),
                default => throw new InvalidArgumentException("Driver [{$slug}] is not supported"),
            };
        }

        return $this->drivers[$slug];
    }
}
