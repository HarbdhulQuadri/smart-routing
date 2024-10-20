<?php

namespace Codehunter\SmartPaymentRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Codehunter\SmartPaymentRouter\Services\PaymentProcessorManager;
use Codehunter\SmartPaymentRouter\Models\PaymentProcessor;
use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentProcessorManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentProcessorManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentProcessorManager = new PaymentProcessorManager();
    }

    public function testGetAvailableProcessors()
    {
        PaymentProcessor::factory()->create(['is_active' => true]);
        PaymentProcessor::factory()->create(['is_active' => false]);

        $availableProcessors = $this->paymentProcessorManager->getAvailableProcessors();

        $this->assertCount(1, $availableProcessors);
        $this->assertTrue($availableProcessors[0]['is_active']);
    }

    public function testGetProcessorByType()
    {
        PaymentProcessor::factory()->create([
            'type' => 'stripe',
            'name' => 'Stripe',
        ]);

        $processor = $this->paymentProcessorManager->getProcessorByType('stripe');

        $this->assertInstanceOf(PaymentProcessorInterface::class, $processor);
        $this->assertEquals('Stripe', $processor->getName());
    }

    public function testAddProcessor()
    {
        $processorData = [
            'name' => 'New Processor',
            'type' => 'new_processor',
            'is_active' => true,
            'transaction_cost' => 2.9,
            'supported_currencies' => ['USD', 'EUR'],
            'supported_countries' => ['US', 'FR'],
        ];

        $processor = $this->paymentProcessorManager->addProcessor($processorData);

        $this->assertInstanceOf(PaymentProcessor::class, $processor);
        $this->assertEquals('New Processor', $processor->name);
        $this->assertEquals('new_processor', $processor->type);
    }

    public function testUpdateProcessor()
    {
        $processor = PaymentProcessor::factory()->create();

        $updateData = [
            'name' => 'Updated Processor',
            'is_active' => false,
        ];

        $result = $this->paymentProcessorManager->updateProcessor($processor->id, $updateData);

        $this->assertTrue($result);
        $this->assertEquals('Updated Processor', $processor->fresh()->name);
        $this->assertFalse($processor->fresh()->is_active);
    }

    public function testRemoveProcessor()
    {
        $processor = PaymentProcessor::factory()->create();

        $result = $this->paymentProcessorManager->removeProcessor($processor->id);

        $this->assertTrue($result);
        $this->assertNull(PaymentProcessor::find($processor->id));
    }
}