<?php

return [
    'default_processor' => 'stripe',
    
    'processors' => [
        'stripe' => [
            'class' => \Codehunter\SmartPaymentRouter\Adapters\StripeAdapter::class,
            'api_key' => env('STRIPE_API_KEY'),
        ],
        'paypal' => [
            'class' => \Codehunter\SmartPaymentRouter\Adapters\PayPalAdapter::class,
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        ],
    ],

    'routing' => [
        'prefer_lowest_cost' => true,
        'fallback_processor' => 'paypal',
    ],

    'logging' => [
        'enabled' => true,
        'channel' => 'smart_payment_router',
    ],
];