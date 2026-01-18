<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Create an invoice/payment link.
     * 
     * @param string $externalId
     * @param float $amount
     * @param string $description
     * @return string|null Payment URL
     */
    public static function createInvoice(string $externalId, float $amount, string $description): ?string
    {
        // Mock implementation
        Log::info("Creating Invoice {$externalId} for {$amount}: {$description}");

        // TODO: Integrate with Xendit/Midtrans API
        // return $response->invoice_url;

        return "https://dummy-payment-gateway.com/pay/{$externalId}";
    }
}
