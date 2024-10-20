<?php

namespace Codehunter\SmartPaymentRouter\Adapters;

use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;
use Codehunter\SmartPaymentRouter\Models\PaymentProcessor;

abstract class BasePaymentProcessorAdapter implements PaymentProcessorInterface
{
    protected $processor;

    public function __construct(PaymentProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function isAvailable(): bool
    {
        return $this->processor->is_active;
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array($currency, $this->processor->supported_currencies);
    }

    public function supportsCountry(string $country): bool
    {
        return in_array($country, $this->processor->supported_countries);
    }

    abstract public function process(array $paymentData): bool;
    abstract public function getTransactionCost(float $amount): float;
}