<?php

namespace Codehunter\SmartPaymentRouter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Codehunter\SmartPaymentRouter\Services\SmartPaymentRouter;
use Codehunter\SmartPaymentRouter\Models\PaymentProcessor;

class SmartPaymentRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected $smartPaymentRouter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smartPaymentRouter = $this->app->make(SmartPaymentRouter::class);
    }

    public function testCompleteRoutingProcess()
    {
        // Create test payment processors
        PaymentProcessor::factory()->create([
            'name' => 'Stripe',
            'type' => 'stripe',
            'is_active' => true,
            'transaction_cost' => 2.9,
            'supported_currencies' => ['USD', 'EUR'],
            'supported_countries' => ['US', 'FR'],
        ]);

        PaymentProcessor::factory()->create([
            'name' => 'PayPal',
            'type' => 'paypal',
            'is_active' => true,
            'transaction_cost' => 3.4,
            'supported_currencies' => ['USD', 'GBP'],
            'supported_countries' => ['US', 'GB'],
        ]);

        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'country' => 'US',
        ];

        $selectedProcessor = $this->smartPaymentRouter->route($paymentData);

        $this->assertInstanceOf(\Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface::class, $selectedProcessor);
        $this->assertEquals('Stripe', $selectedProcessor->getName());
    }

    public function testRoutingWithUnsupportedCurrency()
    {
        PaymentProcessor::factory()->create([
            'name' => 'Stripe',
            'type' => 'stripe',
            'is_active' => true,
            'supported_currencies' => ['USD', 'EUR'],
            'supported_countries' => ['US', 'FR'],
        ]);

        $paymentData = [
            'amount' => 100.00,
            'currency' => 'JPY',
            'country' => 'US',
        ];

        $this->expectException(\Codehunter\SmartPaymentRouter\Exceptions\PaymentRoutingException::class);
        $this->expectExceptionMessage("No suitable payment processor found");

        $this->smartPaymentRouter->route($paymentData);
    }
}