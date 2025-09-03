<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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

class CheckoutController extends Controller
{
    /**
     * Enhanced checkout index with session validation and refresh
     */
    public function index()
    {
        // Get checkout data from session (prepared by cart's proceedToCheckout)
        $checkoutData = session('checkout_items');

        // === DETAILED LOGGING FOR CHECKOUT DATA ===
        Log::info('=== CHECKOUT DATA DEBUG ===');
        Log::info('Raw session data:', ['data' => $checkoutData]);
        Log::info('JSON formatted:', ['json' => json_encode($checkoutData, JSON_PRETTY_PRINT)]);
        Log::info('Data type:', ['type' => gettype($checkoutData)]);

        if (is_array($checkoutData)) {
            Log::info('Array keys:', ['keys' => array_keys($checkoutData)]);

            // Log each key-value pair separately for better readability
            foreach ($checkoutData as $key => $value) {
                Log::info("Key: {$key}", [
                    'value' => $value,
                    'type' => gettype($value)
                ]);
            }
        }

        // Additional session info
        Log::info('Session ID:', ['session_id' => session()->getId()]);
        Log::info('User info:', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'guest'
        ]);
        Log::info('=== END CHECKOUT DATA DEBUG ===');

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

        // Add this before calling getUserAddresses()
        Cache::forget('user_addresses_' . auth()->id());
        // Get user addresses with caching
        $userAddresses = $this->getUserAddresses();

        // Enhanced address debugging
        Log::info('=== START ADDRESS DATA DEBUG ===');
        Log::info('User authenticated:', ['is_auth' => auth()->check()]);
        Log::info('User ID:', ['user_id' => auth()->id()]);
        Log::info('Addresses data type:', ['type' => gettype($userAddresses)]);
        Log::info('Addresses class:', ['class' => get_class($userAddresses)]);
        Log::info('Addresses count:', ['count' => $userAddresses->count()]);
        Log::info('Addresses isEmpty:', ['isEmpty' => $userAddresses->isEmpty()]);
        Log::info('Addresses JSON:', ['json' => json_encode($userAddresses, JSON_PRETTY_PRINT)]);

        // // If collection is not empty, log each address
        // if (!$userAddresses->isEmpty()) {
        //     foreach ($userAddresses as $index => $address) {
        //         Log::info("Address {$index}:", [
        //             'address_data' => $address->toArray(),
        //             'address_id' => $address->id ?? 'no_id',
        //             'is_default' => $address->is_default ?? 'no_default',
        //             'is_active' => $address->is_active ?? 'no_active'
        //         ]);
        //     }
        // }
        Log::info('=== END ADDRESS DATA DEBUG ===');

        // Extract data for view
        return view('frontend.pages.checkout.index', [
            'selectedItems' => $checkoutData['items'],
            'subtotal' => $checkoutData['subtotal'],
            'shipping' => $checkoutData['shipping'],
            'tax' => $checkoutData['tax'],
            'discount' => $checkoutData['discount'],
            'total' => $checkoutData['total'],
            'userAddresses' => $userAddresses,
            'selectedVoucher' => $checkoutData['voucher'] ?? null,
            'lastUpdated' => $checkoutData['created_at']
        ]);
    }

    /**
     * Get user addresses with caching
     */
    private function getUserAddresses()
    {
        Log::info('=== getUserAddresses() START ===');

        if (!auth()->user()) {
            Log::info('No authenticated user found');
            return collect();
        }

        Log::info('User found:', ['user_id' => auth()->id()]);

        $cacheKey = 'user_addresses_' . auth()->id();
        Log::info('Cache key:', ['key' => $cacheKey]);

        // Check if data exists in cache
        if (Cache::has($cacheKey)) {
            Log::info('Cache HIT - returning cached data');
            $cachedData = Cache::get($cacheKey);
            Log::info('Cached data:', ['data' => $cachedData->toArray()]);
        } else {
            Log::info('Cache MISS - querying database');
        }

        $addresses = Cache::remember(
            $cacheKey,
            600,
            function () {
                Log::info('Executing database query for addresses');

                $query = auth()->user()->addresses()
                    ->where('is_active', true)
                    ->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'desc');

                Log::info('Query SQL:', ['sql' => $query->toSql()]);
                Log::info('Query bindings:', ['bindings' => $query->getBindings()]);

                $result = $query->get();

                return $result;
            }
        );

        // Log::info('Final addresses result:', [
        //     'type' => gettype($addresses),
        //     'count' => $addresses->count(),
        //     'data' => $addresses->toArray()
        // ]);
        Log::info('=== getUserAddresses() END ===');

        return $addresses;
    }

    /**
     * Refresh checkout data to ensure current prices and availability
     */
    private function refreshCheckoutData($oldCheckoutData)
    {
        try {
            // Get current cart items based on stored cart_item_ids
            $cartItemIds = collect($oldCheckoutData['items'])->pluck('cart_item_id');
            $currentCartItems = CartItem::whereIn('id', $cartItemIds)
                ->with(['productVariant.product.store'])
                ->get();

            if ($currentCartItems->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Selected items are no longer in your cart'
                ];
            }

            // Validate current items
            $validationResult = $this->validateItemsForCheckout($currentCartItems);
            if (!$validationResult['valid']) {
                return [
                    'success' => false,
                    'message' => 'Some items are no longer available: ' . $validationResult['message']
                ];
            }

            // Prepare fresh checkout data
            $refreshedData = $this->prepareCheckoutData($currentCartItems);

            // Preserve applied voucher if still valid
            if ($oldCheckoutData['voucher']) {
                $voucherValid = $this->isVoucherValid($oldCheckoutData['voucher'], $refreshedData['subtotal']);
                if ($voucherValid) {
                    $refreshedData['voucher'] = $oldCheckoutData['voucher'];
                    $refreshedData['discount'] = $this->calculateVoucherDiscount(
                        $oldCheckoutData['voucher'],
                        $refreshedData['subtotal']
                    );
                    $refreshedData['total'] = $refreshedData['subtotal'] +
                                           $refreshedData['shipping'] +
                                           $refreshedData['tax'] -
                                           $refreshedData['discount'];
                } else {
                    session()->forget('applied_voucher');
                }
            }

            return [
                'success' => true,
                'data' => $refreshedData
            ];

        } catch (\Exception $e) {
            Log::error('Checkout data refresh error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Unable to refresh checkout data'
            ];
        }
    }

    /**
     * Validate items before checkout (enhanced version)
     */
    private function validateItemsForCheckout($items)
    {
        $issues = [];

        foreach ($items as $item) {
            $variant = $item->productVariant;

            // Check if variant exists and is active
            if (!$variant || !$variant->is_active) {
                $issues[] = "Product '" . ($item->productVariant->product->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            // Check if product exists and is active
            if (!$variant->product || !$variant->product->is_active) {
                $issues[] = "Product '" . ($variant->product->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            // Check stock
            if ($variant->stock < $item->quantity) {
                $issues[] = "Only {$variant->stock} units available for '{$variant->product->name}' (you selected {$item->quantity})";
                continue;
            }

            // Check price changes (alert if more than 10% difference)
            $currentPrice = $variant->price;
            $cartPrice = $item->price_when_added;
            if ($currentPrice != $cartPrice) {
                $priceChange = abs($currentPrice - $cartPrice) / $cartPrice;
                if ($priceChange > 0.10) {
                    $issues[] = "Price changed for '{$variant->product->name}': " .
                            number_format($cartPrice) . " → " . number_format($currentPrice);
                }
            }
        }

        return [
            'valid' => empty($issues),
            'message' => implode('; ', $issues)
        ];
    }

    /**
     * Prepare checkout data structure (enhanced version)
     */
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

        // Calculate totals
        $itemsByStore = collect($checkoutItems)->groupBy('store_id');
        $shipping = $this->calculateShippingForItems($itemsByStore);
        $tax = $subtotal * 0.11; // Updated to match your existing 11% tax rate

        // Apply voucher if exists
        $selectedVoucher = session('applied_voucher');
        $discount = 0;

        if ($selectedVoucher) {
            $discount = $this->calculateVoucherDiscount($selectedVoucher, $subtotal);

            // Validate voucher is still applicable
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
        // Simple calculation: 5000 per store
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

    /**
     * API endpoint to refresh checkout totals (for AJAX calls)
     */
    public function refreshTotals(Request $request)
    {
        $checkoutData = session('checkout_items');

        if (!$checkoutData) {
            return response()->json([
                'success' => false,
                'message' => 'No checkout session found'
            ], 400);
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

    // Keep all your existing payment processing methods unchanged...

    public function processCardPayment(Request $request)
    {
        $validated = $request->validate([
            'token_id' => 'required|string|min:3|max:255',
            'authentication_id' => 'required|string|min:3|max:255',
            'first_name' => 'required|string|min:2|max:50|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:50|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email:rfc,dns|max:100',
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/'],
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
        ], [
            'first_name.regex' => 'First name can only contain letters and spaces',
            'last_name.regex' => 'Last name can only contain letters and spaces',
            'phone.regex' => 'Phone number must be a valid Indonesian number',
            'email.dns' => 'Please provide a valid email address',
            'shipping_address_id.required' => 'Please select a shipping address',
            'shipping_address_id.exists' => 'Invalid shipping address selected',
            'billing_address_id.required' => 'Please select a billing address',
            'billing_address_id.exists' => 'Invalid billing address selected',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            $shippingAddress = $user->addresses()->find($validated['shipping_address_id']);
            $billingAddress = $user->addresses()->find($validated['billing_address_id']);

            if (!$shippingAddress || !$billingAddress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid address selection'
                ], 400);
            }

            // Use checkout session data instead of recalculating from cart
            $checkoutData = session('checkout_items');
            if (!$checkoutData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout session expired'
                ], 400);
            }

            // Get cart items based on checkout data
            $cartItemIds = collect($checkoutData['items'])->pluck('cart_item_id');
            $cartItems = CartItem::whereIn('id', $cartItemIds)
                ->with(['productVariant.product.store'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Final validation
            $cartValidation = $this->validateCartItems($cartItems);
            if (!$cartValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart validation failed: ' . $cartValidation['message']
                ], 400);
            }

            // Use checkout session total (server-calculated)
            $validated['amount'] = $checkoutData['total'];
            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

            // Reserve inventory before payment
            $reservationResult = $this->reserveInventory($cartItems);
            if (!$reservationResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory reservation failed: ' . $reservationResult['message']
                ], 400);
            }

            // Create transaction record
            $transaction = $this->createTransaction($validated, 'card', $shippingAddress, $billingAddress);

            // Process payment with Xendit
            $paymentResult = $this->processXenditCardPayment($validated, $transaction);

            if ($paymentResult['success']) {
                // Update transaction with Xendit response
                $transaction->update([
                    'xendit_id' => $paymentResult['xendit_id'] ?? null,
                    'status' => $paymentResult['status'],
                    'xendit_response' => $paymentResult['response']
                ]);

                // Commit inventory reservation
                $this->commitInventoryReservation($cartItems);

                // Create orders (one per store)
                $orders = $this->createOrdersFromCart($cartItems, $transaction);

                // Clear cart and checkout session
                $this->clearCart($user, session()->getId());
                session()->forget('checkout_items');

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'redirect_url' => route('checkout.success', ['transaction' => $transaction->reference_id])
                ]);
            } else {
                // Release inventory reservation
                $this->releaseInventoryReservation($cartItems);

                // Update transaction status to failed
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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Card payment error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed'
            ], 500);
        }
    }

    public function processEwalletPayment(Request $request)
    {
        $validated = $request->validate([
            'channel_code' => 'required|string|in:OVO,DANA,LINKAJA,SHOPEEPAY,GOPAY',
            'first_name' => 'required|string|min:2|max:50|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|min:2|max:50|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email:rfc,dns|max:100',
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/'],
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
        ], [
            'channel_code.in' => 'Please select a valid e-wallet option',
            'first_name.regex' => 'First name can only contain letters and spaces',
            'last_name.regex' => 'Last name can only contain letters and spaces',
            'phone.regex' => 'Phone number must be a valid Indonesian number',
            'shipping_address_id.required' => 'Please select a shipping address',
            'billing_address_id.required' => 'Please select a billing address',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            $shippingAddress = $user->addresses()->find($validated['shipping_address_id']);
            $billingAddress = $user->addresses()->find($validated['billing_address_id']);

            if (!$shippingAddress || !$billingAddress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid address selection'
                ], 400);
            }

            // Use checkout session data
            $checkoutData = session('checkout_items');
            if (!$checkoutData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout session expired'
                ], 400);
            }

            $cartItemIds = collect($checkoutData['items'])->pluck('cart_item_id');
            $cartItems = CartItem::whereIn('id', $cartItemIds)
                ->with(['productVariant.product.store'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Validate cart and use session total
            $cartValidation = $this->validateCartItems($cartItems);
            if (!$cartValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart validation failed: ' . $cartValidation['message']
                ], 400);
            }

            $validated['amount'] = $checkoutData['total'];
            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);

            // Reserve inventory
            $reservationResult = $this->reserveInventory($cartItems);
            if (!$reservationResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory reservation failed: ' . $reservationResult['message']
                ], 400);
            }

            // Create transaction record
            $transaction = $this->createTransaction($validated, 'ewallet', $shippingAddress, $billingAddress);

            // Process payment with Xendit
            $paymentResult = $this->processXenditEwalletPayment($validated, $transaction);

            if ($paymentResult['success']) {
                // Update transaction with Xendit response
                $transaction->update([
                    'xendit_id' => $paymentResult['xendit_id'] ?? null,
                    'status' => 'pending',
                    'xendit_response' => $paymentResult['response']
                ]);

                // Create orders (will be confirmed via webhook)
                $orders = $this->createOrdersFromCart($cartItems, $transaction);

                // Don't clear cart yet for e-wallet (clear after successful webhook)

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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('E-wallet payment error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed'
            ], 500);
        }
    }

    // Keep all your existing helper methods...

    private function validateCartItems($cartItems)
    {
        $issues = [];

        foreach ($cartItems as $item) {
            $variant = $item->productVariant;

            // Check if variant still exists and is active
            if (!$variant || !$variant->is_active) {
                $issues[] = "Product variant '" . ($item->productVariant->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            // Check if product still exists and is active
            if (!$variant->product || !$variant->product->is_active) {
                $issues[] = "Product '" . ($variant->product->name ?? 'Unknown') . "' is no longer available";
                continue;
            }

            // Check stock availability
            if ($variant->stock < $item->quantity) {
                $issues[] = "Only {$variant->stock} units of '{$variant->product->name}' available (requested: {$item->quantity})";
                continue;
            }

            // Check if price has changed significantly (more than 10%)
            $currentPrice = $variant->price;
            $cartPrice = $item->price_when_added;
            $priceChange = abs($currentPrice - $cartPrice) / $cartPrice;

            if ($priceChange > 0.10) {
                $issues[] = "Price of '{$variant->product->name}' has changed from " .
                           number_format($cartPrice) . " to " . number_format($currentPrice);
            }
        }

        return [
            'valid' => empty($issues),
            'message' => implode(', ', $issues)
        ];
    }

    private function normalizePhoneNumber($phone)
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);

        // Convert to +62 format
        if (strpos($phone, '62') === 0) {
            return '+' . $phone;
        } elseif (strpos($phone, '0') === 0) {
            return '+62' . substr($phone, 1);
        } else {
            return '+62' . $phone;
        }
    }

    private function reserveInventory($cartItems)
    {
        try {
            foreach ($cartItems as $item) {
                $variant = $item->productVariant;

                // Check current stock
                if ($variant->stock < $item->quantity) {
                    return [
                        'success' => false,
                        'message' => "Insufficient stock for {$variant->product->name}"
                    ];
                }

                // Reserve stock (you might want to add a reserved_stock column)
                // For now, we'll just verify stock is still available
                $variant->refresh();
                if ($variant->stock < $item->quantity) {
                    return [
                        'success' => false,
                        'message' => "Stock changed during checkout for {$variant->product->name}"
                    ];
                }
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to reserve inventory'
            ];
        }
    }

    private function commitInventoryReservation($cartItems)
    {
        foreach ($cartItems as $item) {
            $variant = $item->productVariant;
            $variant->decrement('stock', $item->quantity);
        }
    }

    private function releaseInventoryReservation($cartItems)
    {
        // If you implement reserved_stock column, release it here
        // For now, just log the release
        Log::info('Inventory reservation released for failed payment');
    }

    private function getCartItems($user = null, $sessionId = null)
    {
        $query = CartItem::with(['productVariant.product.store']);

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->where('expires_at', '>', Carbon::now())
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    private function calculateCartTotal($cartItems)
    {
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->price_when_added;
        });

        $itemsByStore = $cartItems->groupBy(function($item) {
            return $item->productVariant->product->store_id ?? 0;
        });

        $shipping = $this->calculateShipping($itemsByStore);
        $tax = $this->calculateTax($subtotal);
        $discount = $this->calculateDiscount($subtotal);

        return $subtotal + $shipping + $tax - $discount;
    }

    private function calculateShipping($itemsByStore)
    {
        return $itemsByStore->count() * 5000;
    }

    private function calculateTax($subtotal)
    {
        return $subtotal * 0.11;
    }

    private function calculateDiscount($subtotal)
    {
        return 0;
    }

    private function createTransaction($validatedData, $paymentMethod, $shippingAddress = null, $billingAddress = null)
    {
        $referenceId = 'TXN_' . time() . '_' . Str::random(8);

        $transactionData = [
            'reference_id' => $referenceId,
            'amount' => $validatedData['amount'],
            'currency' => 'IDR',
            'payment_method' => $paymentMethod,
            'customer_name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
            'customer_email' => $validatedData['email'],
            'customer_phone' => $validatedData['phone'],
            'status' => 'pending',
            'user_id' => Auth::id(),
        ];

        // Add address information if provided
        if ($shippingAddress) {
            $transactionData['shipping_address_id'] = $shippingAddress->id;
            $transactionData['shipping_address_data'] = json_encode([
                'recipient_name' => $shippingAddress->recipient_name,
                'phone' => $shippingAddress->phone,
                'full_address' => $shippingAddress->full_address,
                'label' => $shippingAddress->label,
            ]);
        }

        if ($billingAddress) {
            $transactionData['billing_address_id'] = $billingAddress->id;
            $transactionData['billing_address_data'] = json_encode([
                'recipient_name' => $billingAddress->recipient_name,
                'phone' => $billingAddress->phone,
                'full_address' => $billingAddress->full_address,
                'label' => $billingAddress->label,
            ]);
        }

        return Transaction::create($transactionData);
    }

    private function validateUserAddresses($user, $shippingAddressId, $billingAddressId)
    {
        $shippingAddress = $user->addresses()->find($shippingAddressId);
        $billingAddress = $user->addresses()->find($billingAddressId);

        if (!$shippingAddress) {
            return [
                'valid' => false,
                'message' => 'Invalid shipping address selected'
            ];
        }

        if (!$billingAddress) {
            return [
                'valid' => false,
                'message' => 'Invalid billing address selected'
            ];
        }

        return [
            'valid' => true,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress
        ];
    }

    private function processXenditCardPayment($validatedData, $transaction)
    {
        try {
            $xenditClient = new \Xendit\XenditSdkPhp\Client(config('xendivel.secret_key'));

            $chargeRequest = new \Xendit\XenditSdkPhp\Payment\ChargeRequest([
                'reference_id' => $transaction->reference_id,
                'amount' => $validatedData['amount'],
                'currency' => 'IDR',
                // ... other Xendit parameters
            ]);

            $response = $xenditClient->payment->createCharge($chargeRequest);

            return [
                'success' => $response->status === 'SUCCEEDED',
                'xendit_id' => $response->id,
                'status' => $response->status,
                'response' => $response->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Xendit card payment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment processing failed',
                'response' => ['error' => $e->getMessage()]
            ];
        }
    }

    private function processXenditEwalletPayment($validatedData, $transaction)
    {
        try {
            // Mock implementation - replace with actual Xendit SDK calls
            return [
                'success' => true,
                'xendit_id' => 'ewc_' . Str::random(20),
                'checkout_url' => 'https://checkout.xendit.co/web/' . Str::random(32),
                'response' => [
                    'id' => 'ewc_' . Str::random(20),
                    'status' => 'pending',
                    'checkout_url' => 'https://checkout.xendit.co/web/' . Str::random(32)
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Xendit e-wallet payment error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment processing failed',
                'response' => ['error' => $e->getMessage()]
            ];
        }
    }

    private function createOrdersFromCart($cartItems, $transaction)
    {
        $itemsByStore = $cartItems->groupBy(function($item) {
            return $item->productVariant->product->store_id ?? 0;
        });

        $orders = collect();

        foreach ($itemsByStore as $storeId => $storeItems) {
            $storeSubtotal = $storeItems->sum(function($item) {
                return $item->quantity * $item->price_when_added;
            });

            $storeShipping = 5000;
            $storeTax = $storeSubtotal * 0.11;
            $storeTotal = $storeSubtotal + $storeShipping + $storeTax;

            $order = Order::create([
                'transaction_id' => $transaction->id,
                'store_id' => $storeId,
                'order_number' => 'ORD_' . time() . '_' . Str::random(6),
                'subtotal' => $storeSubtotal,
                'shipping_cost' => $storeShipping,
                'tax_amount' => $storeTax,
                'total_amount' => $storeTotal,
                'status' => 'pending'
            ]);

            foreach ($storeItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price_when_added,
                    'total_price' => $cartItem->quantity * $cartItem->price_when_added
                ]);
            }

            $orders->push($order);
        }

        return $orders;
    }

    private function clearCart($user = null, $sessionId = null)
    {
        $query = CartItem::query();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        $query->delete();
    }

    public function webhook(Request $request)
    {
        // Verify webhook signature
        // Update transaction status
        // Handle inventory and order updates
        Log::info('Xendit webhook received', $request->all());

        // Basic webhook handling - expand based on your needs
        try {
            $xenditId = $request->input('id');
            $status = $request->input('status');

            $transaction = Transaction::where('xendit_id', $xenditId)->first();

            if ($transaction) {
                $transaction->update([
                    'status' => $status === 'SUCCEEDED' ? 'completed' : 'failed',
                    'xendit_response' => $request->all()
                ]);

                if ($status === 'SUCCEEDED') {
                    // Clear cart for successful e-wallet payments
                    $user = User::find($transaction->user_id);
                    if ($user) {
                        $this->clearCart($user, null);
                        session()->forget('checkout_items');
                    }
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
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
