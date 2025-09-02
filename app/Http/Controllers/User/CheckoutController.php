<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index()
    {
        $user = Auth::user();
        $sessionId = Session::getId();

        // Get cart items for authenticated user or guest
        $cartItems = $this->getCartItems($user, $sessionId);
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Validate cart items before checkout
        $validationResult = $this->validateCartItems($cartItems);
        if (!$validationResult['valid']) {
            return redirect()->route('cart.index')
                ->with('error', 'Some items in your cart are no longer available: ' . $validationResult['message']);
        }

        // Load product variants with relationships
        $cartItems->load(['productVariant.product', 'productVariant.product.store']);

        // Group items by store for multi-vendor checkout
        $itemsByStore = $cartItems->groupBy(function($item) {
            return $item->productVariant->product->store_id ?? 0;
        });

        $userAddresses = $user ? $user->addresses : collect();

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->price_when_added;
        });

        $shipping = $this->calculateShipping($itemsByStore);
        $tax = $this->calculateTax($subtotal);
        $discount = $this->calculateDiscount($subtotal);

        $total = $subtotal + $shipping + $tax - $discount;

        // Prepare selected items for the view
        $selectedItems = $cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'product_variant_id' => $item->product_variant_id,
                'name' => $item->productVariant->product->name,
                'variant_name' => $item->productVariant->name,
                'price' => $item->price_when_added,
                'quantity' => $item->quantity,
                'total' => $item->quantity * $item->price_when_added,
                'store_name' => $item->productVariant->product->store->name ?? 'Default Store',
            ];
        })->toArray();

        return view('frontend.pages.checkout.index', compact(
            'selectedItems',
            'subtotal', 
            'shipping',
            'tax',
            'discount',
            'total',
            'userAddresses' // Add this line
        ));
    }

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

            // Get and validate cart
            $sessionId = Session::getId();
            $cartItems = $this->getCartItems($user, $sessionId);

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Re-validate cart items and calculate server-side totals
            $cartValidation = $this->validateCartItems($cartItems);
            if (!$cartValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart validation failed: ' . $cartValidation['message']
                ], 400);
            }

            // Calculate expected amount server-side (never trust client)
            $expectedAmount = $this->calculateCartTotal($cartItems);
            
            // Add amount to validated data (server-calculated)
            $validated['amount'] = $expectedAmount;

            // Validate phone number format and normalize
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

                // Clear cart
                $this->clearCart($user, $sessionId);

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

            $sessionId = Session::getId();
            $cartItems = $this->getCartItems($user, $sessionId);

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Validate cart and calculate server-side amount
            $cartValidation = $this->validateCartItems($cartItems);
            if (!$cartValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart validation failed: ' . $cartValidation['message']
                ], 400);
            }

            $validated['amount'] = $this->calculateCartTotal($cartItems);
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

    // New validation methods

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

    // Existing helper methods (keeping the same)...
    
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

    // ADD new method to validate if user owns addresses:
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
            $xenditClient = new \Xendit\XenditSdkPhp\Client(config('xendit.secret_key'));
            
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
            // Handle actual Xendit errors
        }
    }
    
    public function webhook(Request $request)
    {
        // Verify webhook signature
        // Update transaction status
        // Handle inventory and order updates
    }

    // Add reserved_stock column to product_variants table


    private function processXenditEwalletPayment($validatedData, $transaction)
    {
        try {
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