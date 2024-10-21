<?php

namespace Codehunter\SmartPaymentRouter\Tests\Unit;

use Codehunter\SmartPaymentRouter\PaymentRouter;
use Codehunter\SmartPaymentRouter\Adapters\StripeAdapter;
use Codehunter\SmartPaymentRouter\Adapters\PayPalAdapter;
use Codehunter\SmartPaymentRouter\Exceptions\PaymentProcessorNotFoundException;

use Orchestra\Testbench\TestCase;

class PaymentRouterTest extends TestCase
{
    protected $config;
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'processors' => [
                'stripe' => [
                    'name' => 'Stripe',
                    'adapter' => StripeAdapter::class,
                    'supported_currencies' => ['USD', 'EUR'],
                    'min_amount' => 0.5,
                    'max_amount' => 999999.99,
                    'base_fee' => 0.30,
                    'percentage_fee' => 0.029,
                    'reliability_score' => 0.99,
                ],
                'paypal' => [
                    'name' => 'PayPal',
                    'adapter' => PayPalAdapter::class,
                    'supported_currencies' => ['USD', 'EUR', 'GBP'],
                    'min_amount' => 1,
                    'max_amount' => 499999.99,
                    'base_fee' => 0.30,
                    'percentage_fee' => 0.034,
                    'reliability_score' => 0.98,
                ],
            ],
        ];

        $this->router = new PaymentRouter($this->config);
    }

    public function testRouteSelectsBestProcessor()
    {
        $transaction = [
            'amount' => 100,
            'currency' => 'USD',
        ];

        $processor = $this->router->route($transaction);

        $this->assertInstanceOf(StripeAdapter::class, $processor);
    }

    public function testRouteThrowsExceptionForUnsupportedCurrency()
    {
        $this->expectException(PaymentProcessorNotFoundException::class);

        $transaction = [
            'amount' => 100,
            'currency' => 'JPY',
        ];

        $this->router->route($transaction);
    }

    public function testAddProcessor()
    {
        $this->router->addProcessor('test', [
            'name' => 'Test',
            'adapter' => StripeAdapter::class,
            'supported_currencies' => ['USD'],
            'min_amount' => 1,
            'max_amount' => 1000,
            'base_fee' => 0.1,
            'percentage_fee' => 0.01,
            'reliability_score' => 0.95,
        ]);

        $info = $this->router->getProcessorInfo('test');
        $this->assertEquals('Test', $info['name']);
    }
    public function testProcessPayment()
    {
        $transaction = [
            'amount' => 100,
            'currency' => 'USD',
        ];
    
        $result = $this->router->processPayment($transaction);
        $this->assertTrue($result);
    }


    public function testUpdateProcessor()
    {
        $this->router->updateProcessor('stripe', [
            'base_fee' => 0.5,
        ]);

        $info = $this->router->getProcessorInfo('stripe');
        $this->assertEquals(0.5, $info['base_fee']);
    }

    public function testRemoveProcessor()
    {
        $this->router->removeProcessor('paypal');

        $this->expectException(PaymentProcessorNotFoundException::class);
        $this->router->getProcessorInfo('paypal');
    }
    
}