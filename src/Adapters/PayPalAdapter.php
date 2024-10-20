<?php

namespace Codehunter\SmartPaymentRouter\Adapters;

class PayPalAdapter extends BasePaymentProcessorAdapter
{
    public function process(array $paymentData): bool
    {
        // Implement PayPal-specific payment processing logic
        return true;
    }

    public function getTransactionCost(float $amount): float
    {
        // Implement PayPal-specific transaction cost calculation
        return $amount * 0.034 + 0.30;
    }
}