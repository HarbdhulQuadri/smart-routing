<?php

namespace VendorName\SmartPaymentRouter\Services;

use Codehunter\SmartPaymentRouter\Contracts\RouterInterface;
use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;
use Codehunter\SmartPaymentRouter\Exceptions\PaymentRoutingException;

class SmartPaymentRouter implements RouterInterface
{
    protected $processorManager;

    public function __construct(PaymentProcessorManager $processorManager)
    {
        $this->processorManager = $processorManager;
    }

    public function route(array $paymentData): PaymentProcessorInterface
    {
        $availableProcessors = $this->processorManager->getAvailableProcessors();
        $suitableProcessors = $this->filterSuitableProcessors($availableProcessors, $paymentData);

        if (empty($suitableProcessors)) {
            throw new PaymentRoutingException("No suitable payment processor found");
        }

        return $this->selectBestProcessor($suitableProcessors, $paymentData['amount']);
    }

    protected function filterSuitableProcessors(array $processors, array $paymentData): array
    {
        return array_filter($processors, function ($processor) use ($paymentData) {
            $processorInstance = $this->processorManager->getProcessorByType($processor['type']);
            return $processorInstance->isAvailable() &&
                   $processorInstance->supportsCurrency($paymentData['currency']) &&
                   $processorInstance->supportsCountry($paymentData['country']);
        });
    }

    protected function selectBestProcessor(array $processors, float $amount): PaymentProcessorInterface
    {
        usort($processors, function ($a, $b) use ($amount) {
            $costA = $this->processorManager->getProcessorByType($a['type'])->getTransactionCost($amount);
            $costB = $this->processorManager->getProcessorByType($b['type'])->getTransactionCost($amount);
            return $costA <=> $costB;
        });

        return $this->processorManager->getProcessorByType($processors[0]['type']);
    }
}