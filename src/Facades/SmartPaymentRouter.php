<?php

namespace Codehunter\SmartPaymentRouter\Facades;

use Illuminate\Support\Facades\Facade;

class SmartPaymentRouter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'smart-payment-router';
    }
}