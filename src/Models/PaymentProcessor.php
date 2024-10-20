<?php

namespace Codehunter\SmartPaymentRouter\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProcessor extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_active',
        'transaction_cost',
        'supported_currencies',
        'supported_countries',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'transaction_cost' => 'float',
        'supported_currencies' => 'array',
        'supported_countries' => 'array',
    ];
}