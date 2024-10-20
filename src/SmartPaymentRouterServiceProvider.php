<?php

namespace Codehunter\SmartPaymentRouter;

use Illuminate\Support\ServiceProvider;
use Codehunter\SmartPaymentRouter\Services\SmartPaymentRouter;
use Codehunter\SmartPaymentRouter\Services\PaymentProcessorManager;

class SmartPaymentRouterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/smart_payment_router.php', 'smart_payment_router');

        $this->app->singleton(PaymentProcessorManager::class, function ($app) {
            return new PaymentProcessorManager();
        });

        $this->app->singleton(SmartPaymentRouter::class, function ($app) {
            return new SmartPaymentRouter($app->make(PaymentProcessorManager::class));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/smart_payment_router.php' => config_path('smart_payment_router.php'),
        ], 'config');
    }
}