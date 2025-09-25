<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Address;
use App\Models\BillingInformation;
use Illuminate\Support\Facades\Validator;
use App\Services\XenditService;

class CheckoutController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    private function configureXenditSSL()
    {
        $certPath = storage_path('app/certificates/cacert-2025-09-09.pem');

        if (file_exists($certPath)) {
            Http::globalOptions(['verify' => $certPath]);
            Log::info('SSL certificate configured', ['cert_path' => $certPath]);
        } else {
            Log::warning('SSL certificate not found', ['cert_path' => $certPath]);

            if (app()->environment('local')) {
                Http::globalOptions(['verify' => false]);
                Log::warning('SSL verification disabled for local development');
            }
        }
    }

    public function index()
    {
        // Get checkout data from session (prepared by cart's proceedToCheckout)
        $checkoutData = session('checkout_items');

        if (!$checkoutData) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout');
        }

        // Check session expiration and refresh if needed
        $createdAt = Carbon::parse($checkoutData['created_at']);
        $minutesElapsed = $createdAt->diffInMinutes(now());

        if ($minutesElapsed > 30) {
            session()->forget('checkout_items');
            return redirect()->route('cart.index')->with('error', 'Checkout session expired. Please select items again.');
        }

        // Re-validate items if session is getting old (15+ minutes)
        if ($minutesElapsed > 15) {
            $refreshResult = $this->refreshCheckoutData($checkoutData);

            if (!$refreshResult['success']) {
                session()->forget('checkout_items');
                return redirect()->route('cart.index')->with('error', $refreshResult['message']);
            }

            $checkoutData = $refreshResult['data'];
            session(['checkout_items' => $checkoutData]);
        }

        // Get user addresses and billing info with fresh cache
        Cache::forget('user_addresses_' . auth()->id());
        Cache::forget('billing_info_' . auth()->id());

        $userAddresses = $this->getUserAddresses();
        $billingInfo = $this->getBillingInfo();

        return view('frontend.pages.checkout.index', [
            'selectedItems' => $checkoutData['items'],
            'subtotal' => $checkoutData['subtotal'],
            'shipping' => $checkoutData['shipping'],
            'tax' => $checkoutData['tax'],
            'discount' => $checkoutData['discount'],
            'total' => $checkoutData['total'],
            'userAddresses' => $userAddresses,
            'selectedVoucher' => $checkoutData['voucher'] ?? null,
            'lastUpdated' => $checkoutData['created_at'],
            'billingInfo' => $billingInfo
        ]);
    }

    private function getBillingInformation($user, $billingInformationId)
    {
        return DB::table('billing_information')
            ->where('id', $billingInformationId)
            ->where('user_id', $user->id)
            ->first();
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
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processing...',
                    'transaction_id' => $transaction->reference_id,
                    'poll_url' => route('checkout.payment-status', $transaction->reference_id)
                ]);
            }

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

    private function processXenditCardPayment($validatedData, $transaction)
    {
        try {
            $referenceId = $transaction->reference_id;

            Log::info('Card payment request', [
                'environment' => config('app.env'),
                'reference_id' => $referenceId,
                'amount' => $validatedData['amount'],
                'customer' => $validatedData['first_name'] . ' ' . $validatedData['last_name']
            ]);

            $paymentRequest = new Request([
                'amount' => (int)$validatedData['amount'],
                'token_id' => $validatedData['token_id'],
                'authentication_id' => $validatedData['authentication_id'],
                'currency' => 'IDR',
                'capture' => true,
                'descriptor' => config('app.name', 'Your Store'),
                'external_id' => $referenceId,
                'billing_details' => [
                    'given_names' => $validatedData['first_name'],
                    'surname' => $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'mobile_number' => $validatedData['phone'],
                ],
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'customer_email' => $validatedData['email'],
                    'order_id' => $referenceId,
                    'source' => 'laravel_app'
                ]
            ]);

            $paymentResponse = Xendivel::payWithCard($paymentRequest);
            $payment = $paymentResponse->getResponse();

            if (is_object($payment)) {
                $payment = json_decode(json_encode($payment), true);
            }

            Log::info('Xendit card payment response', [
                'reference_id' => $referenceId,
                'xendit_id' => $payment['id'] ?? 'no_id',
                'status' => $payment['status'] ?? 'no_status',
            ]);

            $successStatuses = ['CAPTURED', 'PAID', 'SUCCEEDED'];
            $pendingStatuses = ['AUTHORIZED'];
            $currentStatus = $payment['status'] ?? '';

            if (in_array($currentStatus, $pendingStatuses)) {
                return [
                    'success' => true,
                    'xendit_id' => $payment['id'],
                    'status' => 'pending',
                    'response' => $payment
                ];
            } elseif (in_array($currentStatus, $successStatuses)) {
                return [
                    'success' => true,
                    'xendit_id' => $payment['id'],
                    'status' => 'completed',
                    'response' => $payment
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment ' . $currentStatus,
                    'response' => $payment
                ];
            }

        } catch (\Exception $e) {
            Log::error('Xendit card payment error', [
                'reference_id' => $transaction->reference_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'response' => ['error' => $e->getMessage()]
            ];
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

    public function processEwalletPayment(Request $request)
    {
        $validated = $request->validate([
            'channel_code' => 'required|string|in:OVO,DANA,LINKAJA,SHOPEEPAY,GOPAY',
            'address_id' => 'required|exists:addresses,id',
            'billing_information_id' => 'required|exists:billing_information,id',
        ], [
            'channel_code.in' => 'Please select a valid e-wallet option',
            'address_id.required' => 'Please select an address',
            'billing_information_id.required' => 'Please select billing information',
            'billing_information_id.exists' => 'Selected billing information is invalid',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Authentication required'], 401);
            }

            // Get billing information using Eloquent model
            $billingInfo = BillingInformation::where('id', $validated['billing_information_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$billingInfo) {
                return response()->json(['success' => false, 'message' => 'Invalid billing information'], 400);
            }

            // Get single address using Eloquent model
            $address = Address::where('id', $validated['address_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$address) {
                return response()->json(['success' => false, 'message' => 'Invalid address selected'], 400);
            }

            // Use checkout session data
            $checkoutData = session('checkout_items');
            if (!$checkoutData) {
                return response()->json(['success' => false, 'message' => 'Checkout session expired'], 400);
            }

            $cartItemIds = collect($checkoutData['items'])->pluck('cart_item_id');
            $cartItems = CartItem::whereIn('id', $cartItemIds)->with(['productVariant.product.store'])->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
            }

            // Validate cart and use session total
            $cartValidation = $this->validateCartItems($cartItems);
            if (!$cartValidation['valid']) {
                return response()->json(['success' => false, 'message' => 'Cart validation failed: ' . $cartValidation['message']], 400);
            }

            // Prepare data with billing info and normalized phone
            $paymentData = [
                'channel_code' => $validated['channel_code'],
                'amount' => $checkoutData['total'],
                'first_name' => $billingInfo->first_name,
                'last_name' => $billingInfo->last_name,
                'email' => $billingInfo->email,
                'phone' => $this->normalizePhoneNumber($billingInfo->phone),
                'address_id' => $validated['address_id'],
            ];

            // Reserve inventory
            $reservationResult = $this->reserveInventory($cartItems);
            if (!$reservationResult['success']) {
                return response()->json(['success' => false, 'message' => 'Inventory reservation failed: ' . $reservationResult['message']], 400);
            }

            // Create transaction record
            $transaction = $this->createTransaction($paymentData, 'ewallet', $address, $billingInfo);

            // Process payment with Xendit
            $paymentResult = $this->processXenditEwalletPayment($paymentData, $transaction);

            if ($paymentResult['success']) {
                // Update transaction with Xendit response
                $transaction->update([
                    'xendit_id' => $paymentResult['xendit_id'] ?? null,
                    'status' => 'pending',
                    'xendit_response' => $paymentResult['response']
                ]);

                $orders = $this->createOrdersFromCart($cartItems, $transaction);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'checkout_url' => $paymentResult['checkout_url']
                ]);
            } else {
                $this->releaseInventoryReservation($cartItems);

                $transaction->update([
                    'status' => 'failed',
                    'xendit_response' => $paymentResult['response']
                ]);

                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Payment failed'
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('E-wallet payment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment processing failed'], 500);
        }
    }

    private function processXenditEwalletPayment($validatedData, $transaction)
    {
        try {
            $referenceId = $transaction->reference_id;

            Log::info('E-wallet payment request', [
                'environment' => config('app.env'),
                'reference_id' => $referenceId,
                'amount' => $validatedData['amount'],
                'channel_code' => $validatedData['channel_code']
            ]);

            $paymentRequest = new Request([
                'amount' => (int)$validatedData['amount'], // IDR doesn't use cents
                'currency' => 'IDR',
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => $validatedData['channel_code'],
                'external_id' => $referenceId,
                'channel_properties' => [
                    'success_redirect_url' => route('checkout.success', ['transaction' => $referenceId]),
                    'failure_redirect_url' => route('checkout.failed')
                ],
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'customer_email' => $validatedData['email'],
                    'customer_phone' => $validatedData['phone'],
                    'order_id' => $referenceId,
                    'source' => 'laravel_app'
                ]
            ]);

            $paymentResponse = Xendivel::payWithEwallet($paymentRequest);
            $payment = $paymentResponse->getResponse();

            // Convert stdClass to array if needed
            if (is_object($payment)) {
                $payment = json_decode(json_encode($payment), true);
            }

            Log::info('Xendit e-wallet payment response', [
                'reference_id' => $referenceId,
                'xendit_id' => $payment['id'] ?? 'no_id',
                'status' => $payment['status'] ?? 'no_status'
            ]);

            // Check if we got a checkout URL
            if (isset($payment['actions']['desktop_web_checkout_url'])) {
                return [
                    'success' => true,
                    'xendit_id' => $payment['id'],
                    'checkout_url' => $payment['actions']['desktop_web_checkout_url'],
                    'response' => $payment
                ];
            } else {
                Log::error('E-wallet checkout URL not found', [
                    'reference_id' => $referenceId,
                    'payment_data' => $payment
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to initialize e-wallet payment',
                    'response' => $payment
                ];
            }

        } catch (\Exception $e) {
            Log::error('Xendit e-wallet payment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment processing failed',
                'response' => ['error' => $e->getMessage()]
            ];
        }
    }

    private function getBillingInfo()
    {
        if (!auth()->user()) {
            return collect();
        }

        $cacheKey = 'billing_info_' . auth()->id();

        return Cache::remember($cacheKey, 600, function () {
            return BillingInformation::where('user_id', auth()->id())
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    private function getUserAddresses()
    {
        if (!auth()->user()) {
            return collect();
        }

        $cacheKey = 'user_addresses_' . auth()->id();

        return Cache::remember($cacheKey, 600, function () {
            return auth()->user()->addresses()
                ->where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    private function refreshCheckoutData($oldCheckoutData)
    {
        try {
            $cartItemIds = collect($oldCheckoutData['items'])->pluck('cart_item_id');
            $currentCartItems = CartItem::whereIn('id', $cartItemIds)
                ->with(['productVariant.product.store'])
                ->get();

            if ($currentCartItems->isEmpty()) {
                return ['success' => false, 'message' => 'Selected items are no longer in your cart'];
            }

            $validationResult = $this->validateItemsForCheckout($currentCartItems);
            if (!$validationResult['valid']) {
                return ['success' => false, 'message' => 'Some items are no longer available: ' . $validationResult['message']];
            }

            $refreshedData = $this->prepareCheckoutData($currentCartItems);

            // Preserve applied voucher if still valid
            if ($oldCheckoutData['voucher']) {
                $voucherValid = $this->isVoucherValid($oldCheckoutData['voucher'], $refreshedData['subtotal']);
                if ($voucherValid) {
                    $refreshedData['voucher'] = $oldCheckoutData['voucher'];
                    $refreshedData['discount'] = $this->calculateVoucherDiscount($oldCheckoutData['voucher'], $refreshedData['subtotal']);
                    $refreshedData['total'] = $refreshedData['subtotal'] + $refreshedData['shipping'] + $refreshedData['tax'] - $refreshedData['discount'];
                } else {
                    session()->forget('applied_voucher');
                }
            }

            return ['success' => true, 'data' => $refreshedData];

        } catch (\Exception $e) {
            Log::error('Checkout data refresh error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unable to refresh checkout data'];
        }
    }

    private function validateItemsForCheckout($items)
    {
        $issues = [];

        foreach ($items as $item) {
            $variant = $item->productVariant;

            if (!$variant || !$variant->status) {
                $issues[] = "Product '" . ($item->productVariant->product->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            if (!$variant->product || !$variant->product->status) {
                $issues[] = "Product '" . ($variant->product->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            if ($variant->stock < $item->quantity) {
                $issues[] = "Only {$variant->stock} units available for '{$variant->product->name}' (you selected {$item->quantity})";
                continue;
            }

            $currentPrice = $variant->price;
            $cartPrice = $item->price_when_added;
            if ($currentPrice != $cartPrice) {
                $priceChange = abs($currentPrice - $cartPrice) / $cartPrice;
                if ($priceChange > 0.10) {
                    $issues[] = "Price changed for '{$variant->product->name}': " . number_format($cartPrice) . " → " . number_format($currentPrice);
                }
            }
        }

        return ['valid' => empty($issues), 'message' => implode('; ', $issues)];
    }

    private function prepareCheckoutData($items)
    {
        $checkoutItems = [];
        $subtotal = 0;

        foreach ($items as $item) {
            $variant = $item->productVariant;
            $product = $variant->product;
            $store = $product->store;

            $itemTotal = $item->quantity * $item->price_when_added;
            $subtotal += $itemTotal;

            $checkoutItems[] = [
                'cart_item_id' => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'product_id' => $product->id,
                'store_id' => $store->id ?? null,
                'name' => $product->name,
                'variant_name' => $variant->name ?? null,
                'variant_attributes' => $variant->attributes ?? [],
                'price' => $item->price_when_added,
                'current_price' => $variant->price,
                'quantity' => $item->quantity,
                'total' => $itemTotal,
                'image' => $variant->image ?? $product->featured_image,
                'store_name' => $store->name ?? 'Default Store',
                'in_stock' => $variant->stock >= $item->quantity,
                'stock_available' => $variant->stock,
            ];
        }

        $itemsByStore = collect($checkoutItems)->groupBy('store_id');
        $shipping = $this->calculateShippingForItems($itemsByStore);
        $tax = $subtotal * 0.11;

        $selectedVoucher = session('applied_voucher');
        $discount = 0;

        if ($selectedVoucher) {
            $discount = $this->calculateVoucherDiscount($selectedVoucher, $subtotal);

            if (!$this->isVoucherValid($selectedVoucher, $subtotal)) {
                session()->forget('applied_voucher');
                $selectedVoucher = null;
                $discount = 0;
            }
        }

        $total = $subtotal + $shipping + $tax - $discount;

        return [
            'items' => $checkoutItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'voucher' => $selectedVoucher,
            'created_at' => now()->toISOString(),
        ];
    }

    private function calculateShippingForItems($itemsByStore)
    {
        return $itemsByStore->count() * 5000;
    }

    private function calculateVoucherDiscount($voucher, $subtotal)
    {
        if (is_array($voucher)) {
            $discountAmount = $voucher['discount_amount'] ?? 0;
            $discountType = $voucher['discount_type'] ?? 'fixed';
        } else {
            $discountAmount = $voucher->discount_amount ?? 0;
            $discountType = $voucher->discount_type ?? 'fixed';
        }

        if ($discountType === 'percentage') {
            return $subtotal * ($discountAmount / 100);
        }

        return $discountAmount;
    }

    private function isVoucherValid($voucher, $subtotal)
    {
        if (is_array($voucher)) {
            $minAmount = $voucher['minimum_amount'] ?? null;
        } else {
            $minAmount = $voucher->minimum_amount ?? null;
        }

        if ($minAmount && $subtotal < $minAmount) {
            return false;
        }

        return true;
    }

    public function refreshTotals(Request $request)
    {
        $checkoutData = session('checkout_items');

        if (!$checkoutData) {
            return response()->json(['success' => false, 'message' => 'No checkout session found'], 400);
        }

        $refreshResult = $this->refreshCheckoutData($checkoutData);

        if ($refreshResult['success']) {
            session(['checkout_items' => $refreshResult['data']]);

            return response()->json([
                'success' => true,
                'data' => [
                    'subtotal' => $refreshResult['data']['subtotal'],
                    'shipping' => $refreshResult['data']['shipping'],
                    'tax' => $refreshResult['data']['tax'],
                    'discount' => $refreshResult['data']['discount'],
                    'total' => $refreshResult['data']['total'],
                    'items' => $refreshResult['data']['items']
                ]
            ]);
        }

        return response()->json($refreshResult, 400);
    }

    private function validateCartItems($cartItems)
    {
        $issues = [];

        foreach ($cartItems as $item) {
            $variant = $item->productVariant;

            if (!$variant || !$variant->status) {
                $issues[] = "Product variant '" . ($item->productVariant->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            if (!$variant->product || !$variant->product->status) {
                $issues[] = "Product '" . ($variant->product->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            if ($variant->stock < $item->quantity) {
                $issues[] = "Only {$variant->stock} units of '{$variant->product->name}' available (requested: {$item->quantity})";
                continue;
            }

            $currentPrice = $variant->price;
            $cartPrice = $item->price_when_added;
            $priceChange = abs($currentPrice - $cartPrice) / $cartPrice;

            if ($priceChange > 0.10) {
                $issues[] = "Price of '{$variant->product->name}' has changed from " . number_format($cartPrice) . " to " . number_format($currentPrice);
            }
        }

        return ['valid' => empty($issues), 'message' => implode(', ', $issues)];
    }

    private function normalizePhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strpos($phone, '62') === 0) {
            return '+' . $phone;
        } elseif (strpos($phone, '0') === 0) {
            return '+62' . substr($phone, 1);
        } else {
            return '+62' . $phone;
        }
    }

    private function releaseInventoryReservation($cartItems)
    {
        Log::info('Inventory reservation released for failed payment');
    }

    private function validateUserAddresses($user, $shippingAddressId, $billingAddressId)
    {
        $shippingAddress = $user->addresses()->find($shippingAddressId);
        $billingAddress = $user->addresses()->find($billingAddressId);

        if (!$shippingAddress) {
            return ['valid' => false, 'message' => 'Invalid shipping address selected'];
        }

        if (!$billingAddress) {
            return ['valid' => false, 'message' => 'Invalid billing address selected'];
        }

        return [
            'valid' => true,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress
        ];
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

    private function formatFullAddress($address)
    {
        $addressParts = [
            $address->street_address,
            $address->district,
            $address->city,
            $address->province,
            $address->postal_code
        ];

        return implode(', ', array_filter($addressParts));
    }

    public function success(Request $request)
    {
        $transactionRef = $request->get('transaction');
        $transaction = null;

        if ($transactionRef) {
            $transaction = Transaction::where('reference_id', $transactionRef)->first();
        }

        return view('frontend.pages.checkout.success', compact('transaction'));
    }

    public function failed(Request $request)
    {
        $errorMessage = $request->get('error', 'Payment could not be processed. Please try again.');
        return view('frontend.pages.checkout.failed', compact('errorMessage'));
    }
}
