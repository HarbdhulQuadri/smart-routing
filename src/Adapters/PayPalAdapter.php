<?php

namespace Codehunter\SmartPaymentRouter\Adapters;

class PayPalAdapter extends PaymentProcessorAdapter
{
    public function process(array $transaction): bool
    {
        // Implement PayPal-specific processing logic here
        return true;
    }

    public function supportsCurrency(string $currency): bool
    {
        return in_array($currency, $this->config['supported_currencies']);
    }

    public function isWithinLimits(float $amount): bool
    {
        return $amount >= $this->config['min_amount'] && $amount <= $this->config['max_amount'];
    }

    public function calculateCost(float $amount): float
    {
        return $this->config['base_fee'] + ($amount * $this->config['percentage_fee']);
    }

    public function getReliabilityScore(): float
    {
        return $this->config['reliability_score'];
    }
}