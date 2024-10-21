<?php

namespace Codehunter\SmartPaymentRouter\Contracts;

interface PaymentProcessorInterface
{
    public function process(array $transaction): bool;
    public function supportsCurrency(string $currency): bool;
    public function isWithinLimits(float $amount): bool;
    public function calculateCost(float $amount): float;
    public function getReliabilityScore(): float;
    public function getName(): string;
    public function updateConfig(array $newConfig);
    public function getInfo(): array;
}