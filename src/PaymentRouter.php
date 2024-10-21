<?php

namespace Codehunter\SmartPaymentRouter;

use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;
use Codehunter\SmartPaymentRouter\Exceptions\PaymentProcessorNotFoundException;
use Codehunter\SmartPaymentRouter\Exceptions\PaymentProcessingException;
use Codehunter\SmartPaymentRouter\Logging\PaymentLogger;
use Illuminate\Support\Facades\Log;

class PaymentRouter
{
    protected $config;
    protected $processors = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->initializeProcessors();
    }

    protected function initializeProcessors()
    {
        foreach ($this->config['processors'] as $name => $processorConfig) {
            $this->addProcessor($name, $processorConfig);
        }
    }

    public function addProcessor(string $name, array $config)
    {
        $adapterClass = $config['adapter'];
        $this->processors[$name] = new $adapterClass($config);
    }

    public function updateProcessor(string $name, array $config)
    {
        if (!isset($this->processors[$name])) {
            throw new PaymentProcessorNotFoundException("Payment processor '{$name}' not found.");
        }

        $this->processors[$name]->updateConfig($config);
    }

    public function removeProcessor(string $name)
    {
        if (!isset($this->processors[$name])) {
            throw new PaymentProcessorNotFoundException("Payment processor '{$name}' not found.");
        }

        unset($this->processors[$name]);
    }

    public function route(array $transaction)
    {
        $bestProcessor = $this->selectBestProcessor($transaction);

        if (!$bestProcessor) {
            throw new PaymentProcessorNotFoundException("No suitable payment processor found for the transaction.");
        }

        Log::info("Selected payment processor: {$bestProcessor->getName()} for transaction", ['transaction' => $transaction]);

        return $bestProcessor;
    }

    protected function selectBestProcessor(array $transaction)
    {
        $bestProcessor = null;
        $bestScore = -1;

        foreach ($this->processors as $processor) {
            $score = $this->calculateProcessorScore($processor, $transaction);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestProcessor = $processor;
            }
        }

        return $bestProcessor;
    }

    protected function calculateProcessorScore(PaymentProcessorInterface $processor, array $transaction)
    {
        $score = 0;

        // Check currency support
        if ($processor->supportsCurrency($transaction['currency'])) {
            $score += 50;
        } else {
            return -1; // Processor doesn't support the currency, so it's not eligible
        }

        // Check transaction amount limits
        if ($processor->isWithinLimits($transaction['amount'])) {
            $score += 30;
        } else {
            return -1; // Transaction amount is out of processor's limits
        }

        // Calculate cost score (lower cost = higher score)
        $costScore = 100 - ($processor->calculateCost($transaction['amount']) * 10);
        $score += max(0, $costScore);

        // Add reliability score
        $score += $processor->getReliabilityScore() * 10;

        return $score;
    }

    public function getProcessorInfo(string $name)
    {
        if (!isset($this->processors[$name])) {
            throw new PaymentProcessorNotFoundException("Payment processor '{$name}' not found.");
        }

        return $this->processors[$name]->getInfo();
    }

    public function processPayment(array $transaction)
    {
        $processor = $this->route($transaction);

        try {
            $result = $processor->process($transaction);
            PaymentLogger::logTransaction($processor->getName(), $transaction, $result);
            return $result;
        } catch (\Exception $e) {
            PaymentLogger::logTransaction($processor->getName(), $transaction, false);
            throw new PaymentProcessingException("Payment processing failed: " . $e->getMessage());
        }
    }
}