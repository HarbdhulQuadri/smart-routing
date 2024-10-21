<?php

return [
    'processors' => [
        'stripe' => [
            'name' => 'Stripe',
            'adapter' => \Codehunter\SmartPaymentRouter\Adapters\StripeAdapter::class,
            'supported_currencies' => ['USD', 'EUR', 'GBP'],
            'min_amount' => 0.5,
            'max_amount' => 999999.99,
            'base_fee' => 0.30,
            'percentage_fee' => 0.029,
            'reliability_score' => 0.99,
        ],
        'paypal' => [
            'name' => 'PayPal',
            'adapter' => \Codehunter\SmartPaymentRouter\Adapters\PayPalAdapter::class,
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'JPY'],
            'min_amount' => 1,
            'max_amount' => 499999.99,
            'base_fee' => 0.30,
            'percentage_fee' => 0.034,
            'reliability_score' => 0.98,
        ],
    ],
];