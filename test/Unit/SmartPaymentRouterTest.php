<?php

namespace Codehunter\SmartPaymentRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Codehunter\SmartPaymentRouter\Services\SmartPaymentRouter;
use Codehunter\SmartPaymentRouter\Services\PaymentProcessorManager;
use Codehunter\SmartPaymentRouter\Contracts\PaymentProcessorInterface;
use Codehunter\SmartPaymentRouter\Exceptions\PaymentRoutingException;

class SmartPaymentRouterTest extends TestCase
{
    protected $paymentProcessorManager;
    protected $smartPaymentRouter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentProcessorManager = $this->createMock(PaymentProcessorManager::class);
        $this->smartPaymentRouter = new SmartPaymentRouter($this->paymentProcessorManager);
    }

    public function testRouteSelectsLowestCostProcessor()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'country' => 'US',
        ];

        $processor1 = $this->createMock(PaymentProcessorInterface::class);
        $processor1->method('isAvailable')->willReturn(true);
        $processor1->method('supportsCurrency')->willReturn(true);
        $processor1->method('supportsCountry')->willReturn(true);
        $processor1->method('getTransactionCost')->willReturn(3.20);

        $processor2 = $this->createMock(PaymentProcessorInterface::class);
        $processor2->method('isAvailable')->willReturn(true);
        $processor2->method('supportsCurrency')->willReturn(true);
        $processor2->method('supportsCountry')->willReturn(true);
        $processor2->method('getTransactionCost')->willReturn(2.90);

        $this->paymentProcessorManager->method('getAvailableProcessors')
            ->willReturn([
                ['type' => 'processor1'],
                ['type' => 'processor2'],
            ]);

        $this->paymentProcessorManager->method('getProcessorByType')
            ->willReturnMap([
                ['processor1', $processor1],
                ['processor2', $processor2],
            ]);

        $result = $this->smartPaymentRouter->route($paymentData);

        $this->assertSame($processor2, $result);
    }

    public function testRouteThrowsExceptionWhenNoSuitableProcessorFound()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'EUR',
            'country' => 'FR',
        ];

        $processor = $this->createMock(PaymentProcessorInterface::class);
        $processor->method('isAvailable')->willReturn(true);
        $processor->method('supportsCurrency')->willReturn(false);
        $processor->method('supportsCountry')->willReturn(false);

        $this->paymentProcessorManager->method('getAvailableProcessors')
            ->willReturn([['type' => 'processor']]);

        $this->paymentProcessorManager->method('getProcessorByType')
            ->willReturn($processor);

        $this->expectException(PaymentRoutingException::class);
        $this->expectExceptionMessage("No suitable payment processor found");

        $this->smartPaymentRouter->route($paymentData);
    }
}