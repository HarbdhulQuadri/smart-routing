<?php

namespace Codehunter\SmartPaymentRouter\Contracts;
interface RouterInterface
{
    public function route(array $paymentData): PaymentProcessorInterface;
}