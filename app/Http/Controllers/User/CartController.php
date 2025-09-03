<?php

namespace App\Http\Controllers\User;

use App\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\ProductVariant;
use App\Models\CartItem;
use App\Models\PromoCodes;
use App\Models\PromoCodeUsage;
use App\Models\Wishlist;
use Illuminate\Support\Number;

// 11111111111 no longer available


//status

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add item to cart via AJAX - WITH DEBUGGING
     */
    public function addItem(Request $request): JsonResponse
    {
        try {
            // Log the incoming request for debugging
            Log::info('Cart add item request received', [
                'data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validate the request
            $validated = $request->validate([
                'product_variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'integer|min:1|max:99',
                'notes' => 'nullable|string|max:500'
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            // Check if the product variant exists and is active
            $productVariant = ProductVariant::find($validated['product_variant_id']);
            if (!$productVariant) {
                Log::error('Product variant not found', ['variant_id' => $validated['product_variant_id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product variant not found'
                ], 404);
            }

            if ($productVariant->status !== 'active') {
                Log::warning('Product variant not active', [
                    'variant_id' => $validated['product_variant_id'],
                    'status' => $productVariant->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Product variant is not available'
                ], 400);
            }

            // Check stock
            if ($productVariant->stock < ($validated['quantity'] ?? 1)) {
                Log::warning('Insufficient stock', [
                    'variant_id' => $validated['product_variant_id'],
                    'requested' => $validated['quantity'] ?? 1,
                    'available' => $productVariant->stock
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ], 400);
            }

            Log::info('About to call CartService addItem method');

            // Add to cart
            $cartItem = $this->cartService->addItem(
                $validated['product_variant_id'],
                $validated['quantity'] ?? 1
            );

            Log::info('CartService addItem completed', [
                'cart_item_id' => is_object($cartItem) ? $cartItem->id ?? 'no_id' : 'not_object',
                'cart_item_type' => gettype($cartItem)
            ]);

            // Get cart count
            $cartCount = $this->cartService->getCartCount();

            Log::info('Cart count retrieved', ['count' => $cartCount]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'cart_count' => $cartCount,
                'item' => is_object($cartItem) ? [
                    'id' => $cartItem->id ?? null,
                    'quantity' => $cartItem->quantity ?? null,
                    'price' => $cartItem->price_when_added ?? null
                ] : $cartItem,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in cart add', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Add to cart error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(): View
    {
        try {
            Log::info('=== CART INDEX START ===');

            $cartData = $this->cartService->getCartTotals();

            $selectedVoucher = session('applied_voucher', null);

            $subtotal = $cartData['subtotal'] ?? 0;
            $shipping = 5000;
            $tax = $subtotal * 0.08;

            $userId = auth()->id();

            Log::info('Cart Index - Basic Info:', [
                'subtotal' => $subtotal,
                'user_id' => $userId
            ]);

            // Simple test first
            $totalVouchers = PromoCodes::count();
            Log::info('Cart Index - Total vouchers in DB:', ['count' => $totalVouchers]);

            $activeVouchers = PromoCodes::where('is_active', 1)->count();
            Log::info('Cart Index - Active vouchers:', ['count' => $activeVouchers]);

            // Get vouchers step by step
            $step1 = PromoCodes::where('is_active', 1)->get();
            Log::info('Cart Index - Step 1 (active):', ['count' => $step1->count()]);

            $step2 = $step1->where('starts_at', '<=', now());
            Log::info('Cart Index - Step 2 (started):', ['count' => $step2->count()]);

            $step3 = $step2->where('expires_at', '>=', now());
            Log::info('Cart Index - Step 3 (not expired):', ['count' => $step3->count()]);

            // Final query
            $vouchersBeforeUserFilter = PromoCodes::where('is_active', 1)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->where(function($query) {
                    $query->whereNull('usage_limit')
                        ->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->where(function($query) use ($subtotal) {
                    $query->whereNull('minimum_amount')
                        ->orWhere('minimum_amount', '<=', $subtotal);
                })
                ->get();

            Log::info('Cart Index - Vouchers Before User Filter:', [
                'count' => $vouchersBeforeUserFilter->count(),
                'voucher_codes' => $vouchersBeforeUserFilter->pluck('code')->toArray()
            ]);

            $availableVouchers = $vouchersBeforeUserFilter->filter(function($voucher) use ($userId) {
                $canUse = $voucher->canBeUsedByUser($userId);
                Log::info('Cart Index - Testing voucher:', [
                    'code' => $voucher->code,
                    'can_use' => $canUse
                ]);
                return $canUse;
            })->values();

            Log::info('Cart Index - Final Available Vouchers:', [
                'count' => $availableVouchers->count()
            ]);

            $discount = 0;
            if ($selectedVoucher) {
                if (is_array($selectedVoucher)) {
                    $discount = $selectedVoucher['discount_amount'] ?? 0;
                } else {
                    $discount = $selectedVoucher->discount_amount ?? 0;
                }

                if ($selectedVoucher && isset($selectedVoucher['minimum_amount'])) {
                    $minAmount = is_array($selectedVoucher) ?
                        $selectedVoucher['minimum_amount'] :
                        $selectedVoucher->minimum_amount;

                    if ($minAmount && $subtotal < $minAmount) {
                        session()->forget('applied_voucher');
                        $selectedVoucher = null;
                        $discount = 0;
                    }
                }
            }

            $total = $subtotal + $shipping + $tax - $discount;

            $cartItems = $cartData['items'] ?? collect();
            $itemCount = $cartData['item_count'] ?? 0;
            $totalItems = $cartData['total_items'] ?? 0;

            Log::info('=== CART INDEX END ===');

            return view('frontend.pages.cart.index', compact(
                'cartItems',
                'subtotal',
                'shipping',
                'tax',
                'discount',
                'total',
                'itemCount',
                'totalItems',
                'selectedVoucher',
                'availableVouchers'
            ));

        } catch (\Exception $e) {
            Log::error('Cart page error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return view('frontend.pages.cart.index', [
                'cartItems' => collect(),
                'subtotal' => 0,
                'shipping' => 5000,
                'tax' => 0,
                'discount' => 0,
                'total' => 5000,
                'itemCount' => 0,
                'totalItems' => 0,
                'selectedVoucher' => null,
                'availableVouchers' => collect(),
            ]);
        }
    }



    public function updateQuantity(Request $request, string $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        try {
            $success = $this->cartService->updateQuantity($itemId, $validated['quantity']);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found or unauthorized'
                ], 404);
            }

            $cartData = $this->cartService->getCartTotals();

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated successfully',
                'cart_count' => $cartData['item_count'] ?? 0,
                'subtotal' => number_format($cartData['subtotal'] ?? 0, 2),
            ]);

        } catch (\Exception $e) {
            Log::error('Update quantity error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update quantity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart via AJAX
     */
    public function removeItem(string $itemId): JsonResponse
    {
        try {
            $success = $this->cartService->removeItem($itemId);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found or unauthorized'
                ], 404);
            }

            $cartData = $this->cartService->getCartTotals();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $cartData['item_count'] ?? 0,
                'subtotal' => number_format($cartData['subtotal'] ?? 0, 2),
            ]);

        } catch (\Exception $e) {
            Log::error('Remove item error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove multiple items from cart - NEW METHOD
     */
    public function removeMultiple(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'item_ids' => 'required|array',
                'item_ids.*' => 'required|integer|exists:cart_items,id'
            ]);

            $userId = auth()->id();
            $itemIds = $request->input('item_ids');

            // Delete only items that belong to the authenticated user
            $deletedCount = CartItem::where('user_id', $userId)
                ->whereIn('id', $itemIds)
                ->delete();

            Log::info('Bulk delete cart items', [
                'user_id' => $userId,
                'item_ids' => $itemIds,
                'deleted_count' => $deletedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully removed {$deletedCount} item(s) from cart",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove multiple cart items: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove items from cart'
            ], 500);
        }
    }

    /**
     * Apply promo code to cart - NEW METHOD
     */
    public function applyPromo(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'promo_code' => 'required|string|max:50'
            ]);

            $promoCodes = strtoupper(trim($request->input('promo_code')));
            $userId = auth()->id();

            // Check if promo code exists and is valid
            $promo = PromoCodes::where('code', $promoCodes)
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->first();

            if (!$promo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired promo code'
                ]);
            }

            // Check if user has already used this promo code
            $existingUsage = PromoCodeUsage::where('user_id', $userId)
                ->where('promo_code_id', $promo->id)
                ->exists();

            if ($existingUsage && $promo->usage_limit_per_user == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this promo code'
                ]);
            }

            // Store promo code in session for checkout
            session(['applied_promo_code' => $promo->code]);

            Log::info('Promo code applied', [
                'user_id' => $userId,
                'promo_code' => $promo->code,
                'discount_amount' => $promo->discount_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Promo code applied successfully!',
                'promo_code' => $promo->code,
                'discount_amount' => $promo->discount_amount,
                'discount_type' => $promo->discount_type
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to apply promo code: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply promo code'
            ], 500);
        }
    }

    public function getSummary(): JsonResponse
    {
    try {
        $userId = auth()->id();

        $cartItems = CartItem::with([
            'productVariant.product.store',
            'productVariant.attributes'
        ])
        ->where('user_id', $userId)
        ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subtotal' => 0,
                    'shipping' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'item_count' => 0
                ]
            ]);
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price_when_added * $item->quantity;
        });

        $shipping = 9.99;
        $taxRate = 0.04;
        $tax = $subtotal * $taxRate;

        // Apply voucher discount if exists
        $discount = 0;
        if (session('applied_voucher')) {
            $appliedVoucher = session('applied_voucher');
            $discount = $appliedVoucher['discount_amount'] ?? 0;
        }

        $total = $subtotal + $shipping + $tax - $discount;

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => round($subtotal, 2),
                'shipping' => round($shipping, 2),
                'tax' => round($tax, 2),
                'discount' => round($discount, 2),
                'total' => round($total, 2),
                'item_count' => $cartItems->count()
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to get cart summary: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to get cart summary'
        ], 500);
        }
    }
    /**
     * Move item to wishlist - NEW METHOD
     */
    public function moveToWishlist(Request $request, $itemId): JsonResponse
    {
        try {
            $userId = auth()->id();

            $cartItem = CartItem::where('id', $itemId)
                ->where('user_id', $userId)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            // Add to wishlist (assuming you have a wishlist table)
            Wishlist::firstOrCreate([
                'user_id' => $userId,
                'product_variant_id' => $cartItem->product_variant_id
            ]);

            // Remove from cart
            $cartItem->delete();

            Log::info('Item moved to wishlist', [
                'user_id' => $userId,
                'cart_item_id' => $itemId,
                'product_variant_id' => $cartItem->product_variant_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item moved to wishlist successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to move item to wishlist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to move item to wishlist'
            ], 500);
        }
    }

    /**
     * Get cart data for AJAX requests
     */
    public function getCartData(): JsonResponse
    {
        try {
            $cartData = $this->cartService->getCartTotals();

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $cartData['items']->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->productVariant->product->name ?? 'Unknown Product',
                            'variant_name' => $item->productVariant->name ?? '',
                            'quantity' => $item->quantity,
                            'price' => $item->price_when_added,
                            'total' => $item->price_when_added * $item->quantity,
                            'image' => $item->productVariant->image ?? null,
                        ];
                    }),
                    'subtotal' => $cartData['subtotal'] ?? 0,
                    'item_count' => $cartData['item_count'] ?? 0,
                    'total_items' => $cartData['total_items'] ?? 0,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get cart data error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart data: ' . $e->getMessage(),
                'data' => [
                    'items' => [],
                    'subtotal' => 0,
                    'item_count' => 0,
                    'total_items' => 0,
                ]
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): JsonResponse
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_count' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error('Clear cart error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart count for header display
     */
    public function getCartCount(): JsonResponse
    {
        try {
            $count = $this->cartService->getCartCount();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Get cart count error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Failed to get cart count: ' . $e->getMessage()
            ]);
        }
    }

    public function applyVoucher(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucher_id' => 'required|exists:promo_codes,id'
            ]);

            $userId = auth()->id();
            $voucherId = $request->input('voucher_id');

            // Get the promo code
            $promoCodes = PromoCodes::find($voucherId);

            if (!$promoCodes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher not found'
                ], 404);
            }

            // Check if voucher is valid
            if (!$promoCodes->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This voucher has expired or is not active'
                ]);
            }

            // Check if user can use this voucher
            if (!$promoCodes->canBeUsedByUser($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the usage limit for this voucher'
                ]);
            }

            // Get cart totals to check minimum amount
            $cartData = $this->cartService->getCartTotals();
            $subtotal = $cartData['subtotal'] ?? 0;

            if ($promoCodes->minimum_amount && $subtotal < $promoCodes->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum purchase amount is Rp" . number_format($promoCodes->minimum_amount, 2)
                ]);
            }

            // Calculate discount
            $discountAmount = $this->calculateDiscount($promoCodes, $subtotal);

            // Store voucher in session
            session([
                'applied_voucher' => [
                    'id' => $promoCodes->id,
                    'code' => $promoCodes->code,
                    'name' => $promoCodes->name,
                    'description' => $promoCodes->description,
                    'discount_type' => $promoCodes->discount_type,
                    'discount_amount' => $discountAmount,
                    'original_discount' => $promoCodes->discount_amount
                ]
            ]);

            Log::info('Voucher applied successfully', [
                'user_id' => $userId,
                'voucher_id' => $voucherId,
                'voucher_code' => $promoCodes->code,
                'discount_amount' => $discountAmount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voucher applied successfully!',
                'voucher' => [
                    'code' => $promoCodes->code,
                    'name' => $promoCodes->name,
                    'description' => $promoCodes->description,
                    'discount_amount' => $discountAmount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to apply voucher: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply voucher'
            ], 500);
        }
    }

    /**
     * Remove voucher from cart
     */
    public function removeVoucher(Request $request): JsonResponse
    {
        try {
            // Remove voucher from session
            session()->forget('applied_voucher');

            Log::info('Voucher removed from cart', [
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voucher removed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove voucher: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove voucher'
            ], 500);
        }
    }

    public function getAvailableVouchers(): JsonResponse
    {
        try {
            $userId = auth()->id();

            // Get all active vouchers that user can use
            $vouchers = PromoCodes::where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->where(function($query) {
                    $query->whereNull('usage_limit')
                        ->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->get()
                ->filter(function($voucher) use ($userId) {
                    return $voucher->canBeUsedByUser($userId);
                })
                ->map(function($voucher) {
                    return [
                        'id' => $voucher->id,
                        'code' => $voucher->code,
                        'name' => $voucher->name,
                        'description' => $voucher->description,
                        'discount_type' => $voucher->discount_type,
                        'discount_amount' => $voucher->discount_amount,
                        'minimum_amount' => $voucher->minimum_amount,
                        'maximum_discount' => $voucher->maximum_discount,
                        'expires_at' => $voucher->expires_at->format('Y-m-d H:i:s'),
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'vouchers' => $vouchers
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get available vouchers: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load vouchers',
                'vouchers' => []
            ], 500);
        }
    }

    public function validateVoucherByCode(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50'
            ]);

            $userId = auth()->id();
            $code = strtoupper(trim($request->input('code')));

            $promoCodes = PromoCodes::where('code', $code)->first();

            if (!$promoCodes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid voucher code'
                ]);
            }

            if (!$promoCodes->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This voucher has expired or is not active'
                ]);
            }

            if (!$promoCodes->canBeUsedByUser($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the usage limit for this voucher'
                ]);
            }

            $cartData = $this->cartService->getCartTotals();
            $subtotal = $cartData['subtotal'] ?? 0;

            if ($promoCodes->minimum_amount && $subtotal < $promoCodes->minimum_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum purchase amount is Rp" . number_format($promoCodes->minimum_amount, 2)
                ]);
            }

            $discountAmount = $this->calculateDiscount($promoCodes, $subtotal);

            return response()->json([
                'success' => true,
                'message' => 'Voucher is valid',
                'voucher' => [
                    'id' => $promoCodes->id,
                    'code' => $promoCodes->code,
                    'name' => $promoCodes->name,
                    'description' => $promoCodes->description,
                    'discount_type' => $promoCodes->discount_type,
                    'discount_amount' => $discountAmount,
                    'original_discount' => $promoCodes->discount_amount,
                    'minimum_amount' => $promoCodes->minimum_amount,
                    'maximum_discount' => $promoCodes->maximum_discount,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to validate voucher by code: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate voucher'
            ], 500);
        }
    }

    private function calculateDiscount(PromoCodes $promoCodes, float $subtotal): float
    {
        $discountAmount = 0;

        if ($promoCodes->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($promoCodes->discount_amount / 100);

            if ($promoCodes->maximum_discount && $discountAmount > $promoCodes->maximum_discount) {
                $discountAmount = $promoCodes->maximum_discount;
            }
        } else {
            $discountAmount = min($promoCodes->discount_amount, $subtotal);
        }

        return round($discountAmount, 2);
    }
    /**
     * Transfer selected items to checkout
     */
    public function proceedToCheckout(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);
        $checkoutType = $request->input('checkout_type', 'selected'); // 'selected' or 'all'

        if (empty($selectedItems) && $checkoutType === 'selected') {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one item to checkout'
            ], 400);
        }

        try {
            // Get user cart data
            $user = auth()->user();
            $sessionId = session()->getId();
            $cartData = $this->cartService->getCartTotals();
            $allCartItems = $cartData['items'] ?? collect();

            if ($allCartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            // Filter items based on checkout type
            if ($checkoutType === 'all') {
                $itemsForCheckout = $allCartItems;
            } else {
                // Filter only selected items
                $itemsForCheckout = $allCartItems->whereIn('id', $selectedItems);
            }

            if ($itemsForCheckout->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid items selected for checkout'
                ], 400);
            }

            // Validate selected items (stock, availability, etc.)
            $validationResult = $this->validateItemsForCheckout($itemsForCheckout);
            if (!$validationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult['message']
                ], 400);
            }

            // Store selected items in session for checkout
            $checkoutData = $this->prepareCheckoutData($itemsForCheckout);
            session(['checkout_items' => $checkoutData]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('checkout.index'),
                'items_count' => $itemsForCheckout->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout preparation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare checkout. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate items before checkout
     */
    private function validateItemsForCheckout($items)
    {
        $issues = [];

        foreach ($items as $item) {
            $variant = $item->productVariant;

            // Check if variant exists and is active
            if (!$variant || !$variant->status) {
                $issues[] = "Product '" . ($item->productVariant->product->name ?? 'Unknown') . "' is 11111111111 no longer available";
                continue;
            }

            // Check if product exists and is active
            if (!$variant->product || !$variant->product->status) {
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
     * Prepare checkout data structure
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
        $tax = $subtotal * 0.08; // Your tax rate

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

}
