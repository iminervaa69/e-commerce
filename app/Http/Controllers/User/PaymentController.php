<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function processCardPayment(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'token_id' => 'required|string',
                'authentication_id' => 'required|string',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            
            // Generate unique reference ID
            $referenceId = 'ORDER_' . time() . '_' . uniqid();

            // Enhanced logging with environment check
            Log::info('Card payment request received', [
                'environment' => config('app.env'),
                'xendit_env' => config('xendivel.environment', 'not_set'),
                'reference_id' => $referenceId,
                'amount' => $validatedData['amount'],
                'token_id' => $validatedData['token_id'],
                'authentication_id' => $validatedData['authentication_id'],
                'customer' => [
                    'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'email' => $validatedData['email']
                ]
            ]);

            // Format phone number for Xendit
            $phone = $this->formatPhoneNumber($validatedData['phone']);

            // Prepare payment data for Xendivel
            $paymentRequest = new Request([
                'amount' => (int)($validatedData['amount'] * 100), // Convert to cents
                'token_id' => $validatedData['token_id'],
                'authentication_id' => $validatedData['authentication_id'],
                'currency' => 'IDR',
                'descriptor' => config('app.name', 'Your Store'),
                'external_id' => $referenceId, // Important: unique external ID
                'billing_details' => [
                    'given_names' => $validatedData['first_name'],
                    'surname' => $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'mobile_number' => $phone,
                ],
                'metadata' => [
                    'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'customer_email' => $validatedData['email'],
                    'order_id' => $referenceId,
                    'source' => 'laravel_app'
                ]
            ]);

            // Log the exact data being sent to Xendivel
            Log::info('Sending to Xendivel', [
                'reference_id' => $referenceId,
                'amount' => (int)($validatedData['amount'] * 100),
                'external_id' => $referenceId,
                'currency' => 'IDR'
            ]);

            // Save transaction attempt to database BEFORE API call
            $transactionId = $this->saveTransactionAttempt([
                'reference_id' => $referenceId,
                'amount' => $validatedData['amount'],
                'currency' => 'IDR',
                'payment_method' => 'card',
                'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                'customer_email' => $validatedData['email'],
                'customer_phone' => $phone,
                'status' => 'pending'
            ]);

            // Process payment with Xendivel
            $paymentResponse = Xendivel::payWithCard($paymentRequest);
            $payment = $paymentResponse->getResponse();

            // Convert stdClass to array if needed
            if (is_object($payment)) {
                $payment = json_decode(json_encode($payment), true);
            }

            // Enhanced logging with API response
            Log::info('Xendit card payment response', [
                'reference_id' => $referenceId,
                'xendit_id' => isset($payment['id']) ? $payment['id'] : 'no_id',
                'status' => isset($payment['status']) ? $payment['status'] : 'no_status',
                'full_response' => $payment,
                'api_environment' => $this->detectXenditEnvironment($payment)
            ]);

            // Update transaction with Xendit response
            $this->updateTransactionWithResponse($transactionId, $payment);

            // Check if payment was successful
            if (isset($payment['status']) && $payment['status'] === 'CAPTURED') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'redirect_url' => route('payment.success', [
                        'payment_id' => $payment['id'],
                        'reference_id' => $referenceId
                    ]),
                    'payment_data' => $payment
                ]);
            } else {
                $status = $payment['status'] ?? 'unknown';
                Log::warning('Card payment not captured', [
                    'reference_id' => $referenceId,
                    'status' => $status,
                    'payment_data' => $payment
                ]);

                return response()->json([
                    'success' => false,
                    'message' => "Payment {$status}. Please try again or use a different payment method.",
                    'payment_data' => $payment
                ]);
            }

        } catch (\Exception $e) {
            // Enhanced error logging
            Log::error('Card payment processing error', [
                'reference_id' => $referenceId ?? 'not_generated',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->only(['amount', 'first_name', 'last_name', 'email']), // Don't log sensitive data
            ]);

            // Check if it's specifically an authentication error
            if (strpos($e->getMessage(), 'AUTHENTICATION_NOT_FOUND_ERROR') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication expired or invalid. Please try the payment process again.',
                    'error_code' => 'AUTHENTICATION_EXPIRED'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    // Add webhook handler for payment status updates
    public function handleWebhook(Request $request)
    {
        try {
            // Verify webhook (implement Xendit webhook verification)
            $webhookData = $request->all();
            
            Log::info('Xendit webhook received', [
                'event_type' => $webhookData['event_type'] ?? 'unknown',
                'data' => $webhookData
            ]);

            // Update local transaction based on webhook
            if (isset($webhookData['data']['external_id'])) {
                $this->updateTransactionFromWebhook($webhookData);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'webhook_data' => $request->all()
            ]);
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Save transaction attempt to database
     */
    private function saveTransactionAttempt($data)
    {
        return DB::table('transactions')->insertGetId([
            'reference_id' => $data['reference_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'payment_method' => $data['payment_method'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'status' => $data['status'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Update transaction with Xendit response
     */
    private function updateTransactionWithResponse($transactionId, $payment)
    {
        DB::table('transactions')
            ->where('id', $transactionId)
            ->update([
                'xendit_id' => $payment['id'] ?? null,
                'status' => $payment['status'] ?? 'unknown',
                'xendit_response' => json_encode($payment),
                'updated_at' => now()
            ]);
    }

    /**
     * Update transaction from webhook
     */
    private function updateTransactionFromWebhook($webhookData)
    {
        $externalId = $webhookData['data']['external_id'];
        $status = $webhookData['data']['status'] ?? 'unknown';

        DB::table('transactions')
            ->where('reference_id', $externalId)
            ->update([
                'status' => $status,
                'webhook_data' => json_encode($webhookData),
                'updated_at' => now()
            ]);
    }

    /**
     * Detect which Xendit environment was used based on response
     */
    private function detectXenditEnvironment($payment)
    {
        if (isset($payment['id'])) {
            // Test transactions usually have different ID patterns
            return str_contains($payment['id'], 'test') ? 'test' : 'live';
        }
        return 'unknown';
    }

    // ... rest of your existing methods remain the same
    
    public function processEwalletPayment(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'channel_code' => 'required|string',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            
            // Generate unique reference ID
            $referenceId = 'EWALLET_' . time() . '_' . uniqid();

            // Log the incoming request
            Log::info('E-wallet payment request received', [
                'environment' => config('app.env'),
                'xendit_env' => config('xendivel.environment', 'not_set'),
                'reference_id' => $referenceId,
                'amount' => $validatedData['amount'],
                'channel_code' => $validatedData['channel_code'],
                'customer' => [
                    'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'email' => $validatedData['email']
                ]
            ]);

            // Format phone number
            $phone = $this->formatPhoneNumber($validatedData['phone']);

            // Save transaction attempt to database BEFORE API call
            $transactionId = $this->saveTransactionAttempt([
                'reference_id' => $referenceId,
                'amount' => $validatedData['amount'],
                'currency' => 'IDR',
                'payment_method' => 'ewallet_' . $validatedData['channel_code'],
                'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                'customer_email' => $validatedData['email'],
                'customer_phone' => $phone,
                'status' => 'pending'
            ]);

            // Prepare payment data for Xendivel
            $paymentRequest = new Request([
                'amount' => (int)($validatedData['amount'] * 100), // Convert to cents
                'currency' => 'IDR',
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => $validatedData['channel_code'],
                'external_id' => $referenceId, // Important: unique external ID
                'channel_properties' => [
                    'success_redirect_url' => route('payment.ewallet.success'),
                    'failure_redirect_url' => route('payment.ewallet.failed')
                ],
                'metadata' => [
                    'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'customer_email' => $validatedData['email'],
                    'customer_phone' => $phone,
                    'order_id' => $referenceId,
                    'source' => 'laravel_app'
                ]
            ]);

            // Process payment with Xendivel
            $paymentResponse = Xendivel::payWithEwallet($paymentRequest);
            $payment = $paymentResponse->getResponse();

            // Convert stdClass to array if needed
            if (is_object($payment)) {
                $payment = json_decode(json_encode($payment), true);
            }

            // Log the Xendit response
            Log::info('Xendit e-wallet payment response', [
                'reference_id' => $referenceId,
                'xendit_id' => isset($payment['id']) ? $payment['id'] : 'no_id',
                'status' => isset($payment['status']) ? $payment['status'] : 'no_status',
                'full_response' => $payment,
                'api_environment' => $this->detectXenditEnvironment($payment)
            ]);

            // Update transaction with Xendit response
            $this->updateTransactionWithResponse($transactionId, $payment);

            // Check if we got a checkout URL
            if (isset($payment['actions']['desktop_web_checkout_url'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to e-wallet checkout',
                    'checkout_url' => $payment['actions']['desktop_web_checkout_url'],
                    'reference_id' => $referenceId,
                    'payment_data' => $payment
                ]);
            } else {
                Log::error('E-wallet checkout URL not found', [
                    'reference_id' => $referenceId,
                    'payment_data' => $payment
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize e-wallet payment. Please try again.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('E-wallet payment processing error', [
                'reference_id' => $referenceId ?? 'not_generated',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => $request->only(['amount', 'channel_code', 'first_name', 'last_name', 'email'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $referenceId = $request->get('reference_id');
        
        // Log the success page access
        Log::info('Payment success page accessed', [
            'payment_id' => $paymentId,
            'reference_id' => $referenceId
        ]);
        
        return view('frontend.pages.checkout.success', [
            'payment_id' => $paymentId,
            'reference_id' => $referenceId,
            'message' => 'Your payment has been processed successfully!'
        ]);
    }

    public function paymentFailed(Request $request)
    {
        $error = $request->get('error', 'Payment failed');
        
        return view('frontend.pages.checkout.failed', [
            'error' => $error,
            'message' => 'Your payment could not be processed.'
        ]);
    }

    public function ewalletSuccess(Request $request)
    {
        // E-wallet success redirect
        // The actual payment status will be confirmed via webhook
        return view('frontend.pages.checkout.success', [
            'message' => 'Your e-wallet payment is being processed. You will receive a confirmation shortly.'
        ]);
    }

    public function ewalletFailed(Request $request)
    {
        return view('frontend.pages.checkout.failed', [
            'message' => 'Your e-wallet payment could not be processed.'
        ]);
    }

    /**
     * Format phone number for Xendit (Indonesian format)
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Remove leading zero if present
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        
        // Add Indonesia country code if not present
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        
        return '+' . $phone;
    }
}