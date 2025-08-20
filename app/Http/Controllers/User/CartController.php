<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the cart page
     */
    public function index(): View
    {
        try {
            $cartData = $this->cartService->getCartTotals();
            
            // Ensure all required variables are set with defaults
            $cartItems = $cartData['items'] ?? collect();
            $subtotal = $cartData['subtotal'] ?? 0;
            $itemCount = $cartData['item_count'] ?? 0;
            $totalItems = $cartData['total_items'] ?? 0;

            return view('frontend.pages.cart', compact(
                'cartItems',
                'subtotal', 
                'itemCount',
                'totalItems'
            ));

        } catch (\Exception $e) {
            Log::error('Cart page error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Return with empty cart data as fallback
            return view('frontend.pages.cart', [
                'cartItems' => collect(),
                'subtotal' => 0,
                'itemCount' => 0,
                'totalItems' => 0,
            ]);
        }
    }

    /**
     * Add item to cart via AJAX
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'integer|min:1|max:99',
        ]);

        try {
            $cartItem = $this->cartService->addItem(
                $validated['product_variant_id'],
                $validated['quantity'] ?? 1
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'cart_count' => $this->cartService->getCartCount(),
                'item' => $cartItem,
            ]);

        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update item quantity via AJAX
     */
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
}