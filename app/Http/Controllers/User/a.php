<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\Address;
use App\Models\BillingInformation;
use App\Services\XenditService;

class CheckoutController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function processCardPayment(Request $request)
    {
        $validated = $request->validate([
            'card_number' => 'required|string|regex:/^[0-9]{13,19}$/',
            'expiry_month' => 'required|integer|between:1,12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'cvv' => 'required|string|regex:/^[0-9]{3,4}$/',
            'cardholder_name' => 'required|string|max:255',
            'address_id' => 'required|exists:addresses,id',
            'billing_information_id' => 'required|exists:billing_information,id',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Authentication required'], 401);
            }

            // Get billing information and address
            $billingInfo = BillingInformation::where('id', $validated['billing_information_id'])
                ->where('user_id', $user->id)
                ->first();

            $address = Address::where('id', $validated['address_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$billingInfo || !$address) {
                return response()->json(['success' => false, 'message' => 'Invalid billing information or address'], 400);
            }

            // Get checkout data
            $checkoutData = session('checkout_items');
            if (!$checkoutData) {
                return response()->json(['success' => false, 'message' => 'Checkout session expired'], 400);
            }

            // Validate cart items
            $cartItemIds = collect($checkoutData['items'])->pluck('cart_item_id');
            $cartItems = CartItem::whereIn('id', $cartItemIds)->with(['productVariant.product.store'])->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
            }

            $cartValidation = $this->validateCartItems($cartItems);
            if (!$cartValidation['valid']) {
                return response()->json(['success' => false, 'message' => 'Cart validation failed: ' . $cartValidation['message']], 400);
            }

            // Create transaction record first
            $referenceId = 'TXN_' . time() . '_' . uniqid();
            $transaction = $this->createTransaction([
                'amount' => $checkoutData['total'],
                'first_name' => $billingInfo->first_name,
                'last_name' => $billingInfo->last_name,
                'email' => $billingInfo->email,
                'phone' => $this->normalizePhoneNumber($billingInfo->phone),
                'address_id' => $validated['address_id'],
            ], 'card', $address, $billingInfo, $referenceId);

            // Step 1: Create Payment Method with Xendit
            $paymentMethodResponse = $this->xenditService->createPaymentMethod([
                'card_number' => $validated['card_number'],
                'expiry_month' => $validated['expiry_month'],
                'expiry_year' => $validated['expiry_year'],
                'cardholder_name' => $validated['cardholder_name'],
                'cvv' => $validated['cvv'],
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'customer_name' => $billingInfo->first_name . ' ' . $billingInfo->last_name,
                    'customer_email' => $billingInfo->email
                ]
            ]);

            // Step 2: Create Payment Request
            $paymentRequestResponse = $this->xenditService->createPaymentRequest(
                $paymentMethodResponse['id'],
                (int)$checkoutData['total'],
                $referenceId,
                [
                    'transaction_id' => $transaction->id,
                    'customer_name' => $billingInfo->first_name . ' ' . $billingInfo->last_name,
                    'customer_email' => $billingInfo->email,
                    'cart_item_ids' => $cartItemIds->toArray() // Important for webhook
                ]
            );

            // Update transaction with Xendit IDs
            $transaction->update([
                'xendit_payment_method_id' => $paymentMethodResponse['id'],
                'xendit_payment_request_id' => $paymentRequestResponse['id'],
                'status' => 'pending',
                'xendit_response' => [
                    'payment_method' => $paymentMethodResponse,
                    'payment_request' => $paymentRequestResponse
                ]
            ]);

            DB::commit();

            // Check payment status
            $paymentStatus = $paymentRequestResponse['status'];

            if (in_array($paymentStatus, ['SUCCEEDED', 'CAPTURED'])) {
                // Payment successful immediately
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'transaction_id' => $transaction->reference_id,
                    'redirect_url' => route('checkout.success', $transaction->reference_id)
                ]);
            } elseif (in_array($paymentStatus, ['REQUIRES_ACTION'])) {
                // 3DS authentication required
                $actions = $paymentRequestResponse['actions'] ?? [];
                $nextAction = collect($actions)->first();

                if ($nextAction && isset($nextAction['url'])) {
                    return response()->json([
                        'success' => true,
                        'requires_action' => true,
                        'action_url' => $nextAction['url'],
                        'transaction_id' => $transaction->reference_id
                    ]);
                }
            } elseif ($paymentStatus === 'PENDING') {
                // Waiting for webhook
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processing...',
                    'transaction_id' => $transaction->reference_id,
                    'poll_url' => route('checkout.payment-status', $transaction->reference_id)
                ]);
            }

            // Payment failed
            $transaction->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . ($paymentRequestResponse['failure_code'] ?? 'Unknown error')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Card payment error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            return response()->json(['success' => false, 'message' => 'Payment processing failed'], 500);
        }
    }

    public function checkPaymentStatus($transactionRef)
    {
        $transaction = Transaction::where('reference_id', $transactionRef)->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        // If we have a Xendit payment request ID, check the latest status
        if ($transaction->xendit_payment_request_id) {
            try {
                $paymentRequest = $this->xenditService->getPaymentRequest($transaction->xendit_payment_request_id);

                // Update local status if different
                if ($paymentRequest['status'] !== $transaction->status) {
                    $transaction->update(['status' => strtolower($paymentRequest['status'])]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to check payment status from Xendit', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'redirect_url' => $this->getRedirectUrl($transaction)
        ]);
    }

    private function getRedirectUrl($transaction)
    {
        switch ($transaction->status) {
            case 'succeeded':
            case 'captured':
            case 'completed':
                return route('checkout.success', $transaction->reference_id);
            case 'failed':
            case 'expired':
                return route('checkout.failed');
            default:
                return null;
        }
    }

    private function createTransaction($data, $paymentMethod, $address, $billingInfo, $referenceId)
    {
        return Transaction::create([
            'reference_id' => $referenceId,
            'total_amount' => $data['amount'],
            'currency' => 'IDR',
            'payment_method' => $paymentMethod,
            'customer_name' => $data['first_name'] . ' ' . $data['last_name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'status' => 'pending',
            'user_id' => Auth::id(),
            'address_id' => $address->id,
            'billing_information_id' => $billingInfo->id,
            'address_data' => json_encode([
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'full_address' => $address->full_address,
                'label' => $address->label,
            ]),
            'billing_information_data' => json_encode([
                'first_name' => $billingInfo->first_name,
                'last_name' => $billingInfo->last_name,
                'email' => $billingInfo->email,
                'phone' => $billingInfo->phone,
            ]),
        ]);
    }
}
