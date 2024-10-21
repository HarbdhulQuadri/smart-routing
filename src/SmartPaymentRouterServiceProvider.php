<?php

namespace Codehunter\SmartPaymentRouter;

use Illuminate\Support\ServiceProvider;

class SmartPaymentRouterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/smart_payment_router.php', 'smart_payment_router');

        $this->app->singleton(PaymentRouter::class, function ($app) {
            return new PaymentRouter($app['config']['smart_payment_router']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/smart_payment_router.php' => config_path('smart_payment_router.php'),
        ], 'config');
    }
}