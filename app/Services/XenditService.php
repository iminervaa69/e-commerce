<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\PaymentMethod\PaymentMethodApi;
use Xendit\PaymentRequest\PaymentRequestApi;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected $paymentMethodApi;
    protected $paymentRequestApi;

    public function __construct()
    {
        // Initialize the SDK with your API key
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        // Initialize the API clients
        $this->paymentMethodApi = new PaymentMethodApi();
        $this->paymentRequestApi = new PaymentRequestApi();
    }

    public function createPaymentMethod($cardData)
    {
        try {
            $params = [
                'type' => 'CARD',
                'reusability' => 'ONE_TIME_USE',
                'card' => [
                    'channel_code' => 'CARD',
                    'card_information' => [
                        'card_number' => $cardData['card_number'],
                        'expiry_month' => $cardData['expiry_month'],
                        'expiry_year' => $cardData['expiry_year'],
                        'cardholder_name' => $cardData['cardholder_name'],
                        'cvv' => $cardData['cvv']
                    ]
                ],
                'metadata' => $cardData['metadata'] ?? []
            ];

            return PaymentMethod::create($params);
        } catch (\Exception $e) {
            Log::error('Xendit create payment method error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    public function createPaymentRequest($paymentMethodId, $amount, $referenceId, $metadata = [])
    {
        try {
            $params = [
                'reference_id' => $referenceId,
                'amount' => $amount,
                'currency' => 'IDR',
                'country' => 'ID',
                'payment_method' => [
                    'id' => $paymentMethodId,
                    'type' => 'CARD'
                ],
                'metadata' => $metadata
            ];

            return PaymentRequest::create($params);
        } catch (\Exception $e) {
            Log::error('Xendit create payment request error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'reference_id' => $referenceId
            ]);
            throw $e;
        }
    }

    public function getPaymentRequest($paymentRequestId)
    {
        try {
            return PaymentRequest::get($paymentRequestId);
        } catch (\Exception $e) {
            Log::error('Xendit get payment request error', [
                'error' => $e->getMessage(),
                'payment_request_id' => $paymentRequestId
            ]);
            throw $e;
        }
    }
}
