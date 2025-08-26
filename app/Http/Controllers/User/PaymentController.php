<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

            // Log the incoming request for debugging
            Log::info('Card payment request received', [
                'amount' => $validatedData['amount'],
                'amount_type' => gettype($validatedData['amount']),
                'token_id' => $validatedData['token_id'],
                'customer' => [
                    'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'email' => $validatedData['email']
                ]
            ]);

            // Format phone number for Xendit (ensure it starts with +62 for Indonesia)
            $phone = $this->formatPhoneNumber($validatedData['phone']);

            // Prepare payment data for Xendivel
            $paymentRequest = new Request([
                'amount' => (int)($validatedData['amount'] * 100), // Convert to cents
                'token_id' => $validatedData['token_id'],
                'authentication_id' => $validatedData['authentication_id'],
                'currency' => 'IDR', // or your preferred currency
                'descriptor' => config('app.name', 'Your Store'),
                'billing_details' => [
                    'given_names' => $validatedData['first_name'],
                    'surname' => $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'mobile_number' => $phone,
                ],
                'metadata' => [
                    'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'customer_email' => $validatedData['email'],
                    'order_id' => 'ORDER_' . time(), // You might want to generate this differently
                ]
            ]);

            // Process payment with Xendivel
            $payment = Xendivel::payWithCard($paymentRequest)->getResponse();

            // Log the Xendit response
            Log::info('Xendit card payment response', [
                'status' => $payment['status'] ?? 'unknown',
                'id' => $payment['id'] ?? 'no_id',
                'external_id' => $payment['external_id'] ?? 'no_external_id'
            ]);

            // Check if payment was successful
            if (isset($payment['status']) && $payment['status'] === 'CAPTURED') {
                // Payment successful
                // Here you would typically:
                // 1. Save the payment to your database
                // 2. Update order status
                // 3. Send confirmation email (Xendivel can handle this)

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'redirect_url' => route('frontend.pages.checkout.success', ['payment_id' => $payment['id']]),
                    'payment_data' => $payment
                ]);
            } else {
                // Payment failed or pending
                $status = $payment['status'] ?? 'unknown';
                Log::warning('Card payment not captured', [
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
            Log::error('Card payment processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

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

            // Log the incoming request
            Log::info('E-wallet payment request received', [
                'amount' => $validatedData['amount'],
                'channel_code' => $validatedData['channel_code'],
                'customer' => [
                    'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'email' => $validatedData['email']
                ]
            ]);

            // Prepare payment data for Xendivel
            $paymentRequest = new Request([
                'amount' => (int)($validatedData['amount'] * 100), // Convert to cents
                'currency' => 'IDR', // or your preferred currency
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => $validatedData['channel_code'],
                'channel_properties' => [
                    'success_redirect_url' => "",
                    'failure_redirect_url' => ""
                ],
                'metadata' => [
                    'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'customer_email' => $validatedData['email'],
                    'customer_phone' => $validatedData['phone'],
                    'order_id' => 'ORDER_' . time(),
                ]
            ]);

            // Process payment with Xendivel
            $payment = Xendivel::payWithEwallet($paymentRequest)->getResponse();

            // Log the Xendit response
            Log::info('Xendit e-wallet payment response', $payment);

            // Check if we got a checkout URL
            if (isset($payment['actions']['desktop_web_checkout_url'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to e-wallet checkout',
                    'checkout_url' => $payment['actions']['desktop_web_checkout_url'],
                    'payment_data' => $payment
                ]);
            } else {
                Log::error('E-wallet checkout URL not found', ['payment_data' => $payment]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize e-wallet payment. Please try again.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('E-wallet payment processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
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
        
        return view('frontend.pages.checkout.success', [
            'payment_id' => $paymentId,
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