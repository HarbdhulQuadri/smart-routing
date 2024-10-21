<?php

namespace Codehunter\SmartPaymentRouter\Logging;

use Illuminate\Support\Facades\Log;

class PaymentLogger
{
    public static function logTransaction(string $processorName, array $transaction, bool $success)
    {
        $status = $success ? 'successful' : 'failed';
        $message = "Payment {$status} using {$processorName}";
        
        Log::info($message, [
            'processor' => $processorName,
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'success' => $success,
        ]);
    }
}
