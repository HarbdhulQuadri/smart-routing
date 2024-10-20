<?php

namespace Codehunter\SmartPaymentRouter\Services;

use Codehunter\SmartPaymentRouter\Models\PaymentProcessor;
use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;

class PaymentProcessorManager
{
    public function getAvailableProcessors(): array
    {
        return PaymentProcessor::where('is_active', true)->get()->toArray();
    }

    public function getProcessorByType(string $type): ?PaymentProcessorInterface
    {
        $processor = PaymentProcessor::where('type', $type)->first();
        if (!$processor) {
            return null;
        }

        $adapterClass = "Codehunter\\SmartPaymentRouter\\Adapters\\{$type}Adapter";
        return new $adapterClass($processor);
    }

    public function addProcessor(array $data): PaymentProcessor
    {
        return PaymentProcessor::create($data);
    }

    public function updateProcessor(int $id, array $data): bool
    {
        $processor = PaymentProcessor::find($id);
        if (!$processor) {
            return false;
        }
        return $processor->update($data);
    }

    public function removeProcessor(int $id): bool
    {
        $processor = PaymentProcessor::find($id);
        if (!$processor) {
            return false;
        }
        return $processor->delete();
    }
}