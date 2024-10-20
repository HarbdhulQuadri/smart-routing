<?php

namespace Codehunter\SmartPaymentRouter\Adapters;

class StripeAdapter extends BasePaymentProcessorAdapter
{
    public function process(array $paymentData): bool
    {
        // Implement Stripe-specific payment processing logic
        return true;
    }

    public function getTransactionCost(float $amount): float
    {
        // Implement Stripe-specific transaction cost calculation
        return $amount * 0.029 + 0.30;
    }
}