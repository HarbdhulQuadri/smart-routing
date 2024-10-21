<?php

namespace Codehunter\SmartPaymentRouter\Adapters;

use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;

abstract class PaymentProcessorAdapter implements PaymentProcessorInterface
{
    protected $config;
    protected $name;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->name = $config['name'];
    }

    abstract public function process(array $transaction): bool;

    abstract public function supportsCurrency(string $currency): bool;

    abstract public function isWithinLimits(float $amount): bool;

    abstract public function calculateCost(float $amount): float;

    abstract public function getReliabilityScore(): float;

    public function getName(): string
    {
        return $this->name;
    }

    public function updateConfig(array $newConfig)
    {
        $this->config = array_merge($this->config, $newConfig);
    }

    public function getInfo(): array
    {
        return [
            'name' => $this->name,
            'supported_currencies' => $this->config['supported_currencies'],
            'min_amount' => $this->config['min_amount'],
            'max_amount' => $this->config['max_amount'],
            'base_fee' => $this->config['base_fee'],
            'percentage_fee' => $this->config['percentage_fee'],
        ];
    }
}