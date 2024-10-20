<?php

namespace Codehunter\SmartPaymentRouter\Contracts;

interface PaymentProcessorInterface
{
    public function process(array $paymentData): bool;
    public function getTransactionCost(float $amount): float;
    public function isAvailable(): bool;
    public function supportsCurrency(string $currency): bool;
    public function supportsCountry(string $country): bool;
}
