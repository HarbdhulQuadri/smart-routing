# Smart Payment Router

Smart Payment Router is a Laravel package that provides intelligent payment routing capabilities for your application. It dynamically selects the best payment processor for each transaction based on factors such as transaction cost, reliability, and currency support.

## Features

- Dynamic routing logic to select the best payment processor for each transaction
- Configurable routing rules
- Easy management of payment processors (add, update, remove)
- Adapter pattern for easy integration of new payment processors
- Logging and monitoring mechanisms
- Robust error handling
- Secure handling of sensitive payment information
- Compatible with Laravel 10.x
- Comprehensive test suite

## Installation

You can install the package via composer:

```bash
composer require codehunter/smart-payment-router
```

## Configuration

After installation, publish the configuration file:

```bash
php artisan vendor:publish --provider="Codehunter\SmartPaymentRouter\SmartPaymentRouterServiceProvider" --tag="config"
```

This will create a `config/smart_payment_router.php` file. You can modify this file to add or configure payment processors.

## Usage

To use the Smart Payment Router in your application:

```php
use Codehunter\SmartPaymentRouter\PaymentRouter;

class PaymentController extends Controller
{
    protected $paymentRouter;

    public function __construct(PaymentRouter $paymentRouter)
    {
        $this->paymentRouter = $paymentRouter;
    }

    public function processPayment(Request $request)
    {
        $transaction = [
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
        ];

        try {
            $processor = $this->paymentRouter->route($transaction);
            $result = $processor->process($transaction);
            
            if ($result) {
                return response()->json(['message' => 'Payment processed successfully']);
            } else {
                return response()->json(['error' => 'Payment processing failed'], 400);
            }
        } catch (PaymentProcessorNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

## Adding a New Payment Processor

To add a new payment processor:

1. Create a new adapter class that extends `PaymentProcessorAdapter`.
2. Implement the required methods in your adapter class.
3. Add the new processor to your configuration file.

Example of adding a new processor in the configuration:

```php
'new_processor' => [
    'name' => 'New Processor',
    'adapter' => \App\PaymentProcessors\NewProcessorAdapter::class,
    'supported_currencies' => ['USD', 'EUR'],
    'min_amount' => 1,
    'max_amount' => 10000,
    'base_fee' => 0.30,
    'percentage_fee' => 0.025,
    'reliability_score' => 0.97,
],
```

## Security

The Smart Payment Router package follows security best practices for handling sensitive payment information. However, it's crucial to ensure that your entire application adheres to PCI DSS standards when dealing with payment data.

## Testing

To run the package tests:

```bash
vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The Smart Payment Router package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).