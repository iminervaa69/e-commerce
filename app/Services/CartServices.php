<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CartService
{
    /**
     * Add item to cart (works for both guest and authenticated users)
     */
    public function addItem(int $productVariantId, int $quantity = 1, float $priceWhenAdded = null): mixed
    {
        // Validate product exists and get current price
        $productVariant = ProductVariant::findOrFail($productVariantId);
        $priceWhenAdded = $priceWhenAdded ?? $productVariant->price;

        if (Auth::check()) {
            return $this->addItemForUser(Auth::id(), $productVariantId, $quantity, $priceWhenAdded);
        } else {
            return $this->addItemForGuest($productVariantId, $quantity, $priceWhenAdded);
        }
    }

    /**
     * Add item for authenticated user
     */
    private function addItemForUser(int $userId, int $productVariantId, int $quantity, float $priceWhenAdded): CartItem
    {
        $existingItem = CartItem::where('user_id', $userId)
            ->where('product_variant_id', $productVariantId)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            $existingItem->extendExpiration();
            return $existingItem->fresh();
        }

        return CartItem::create([
            'user_id' => $userId,
            'product_variant_id' => $productVariantId,
            'quantity' => $quantity,
            'price_when_added' => $priceWhenAdded,
        ]);
    }

    /**
     * Add item for guest user (database first, session fallback)
     */
    private function addItemForGuest(int $productVariantId, int $quantity, float $priceWhenAdded): mixed
    {
        $sessionId = Session::getId();

        try {
            // Try database first
            $existingItem = CartItem::forSession($sessionId)
                ->where('product_variant_id', $productVariantId)
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $quantity);
                $existingItem->extendExpiration();
                return $existingItem->fresh();
            }

            return CartItem::create([
                'session_id' => $sessionId,
                'product_variant_id' => $productVariantId,
                'quantity' => $quantity,
                'price_when_added' => $priceWhenAdded,
            ]);

        } catch (\Exception $e) {
            // Fallback to session storage
            Log::warning('Failed to store guest cart in database, using session fallback', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'product_variant_id' => $productVariantId
            ]);

            return $this->addItemToSession($productVariantId, $quantity, $priceWhenAdded);
        }
    }

    /**
     * Session storage fallback
     */
    private function addItemToSession(int $productVariantId, int $quantity, float $priceWhenAdded): array
    {
        $cart = Session::get('guest_cart', []);
        
        $existingIndex = collect($cart)->search(function ($item) use ($productVariantId) {
            return $item['product_variant_id'] === $productVariantId;
        });

        if ($existingIndex !== false) {
            $cart[$existingIndex]['quantity'] += $quantity;
            $cart[$existingIndex]['updated_at'] = now()->toISOString();
        } else {
            $cart[] = [
                'id' => 'session_' . uniqid(),
                'product_variant_id' => $productVariantId,
                'quantity' => $quantity,
                'price_when_added' => $priceWhenAdded,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
                'expires_at' => now()->addHours(24)->toISOString(),
            ];
        }
        
        Session::put('guest_cart', $cart);
        return end($cart);
    }

    /**
     * Get all cart items for current user/session
     */
    public function getCartItems(): Collection
    {
        if (Auth::check()) {
            return CartItem::forUser(Auth::id())
                ->active()
                ->with('productVariant.product')
                ->get();
        }

        return $this->getGuestCartItems();
    }

    /**
     * Get guest cart items (database first, session fallback)
     */
    private function getGuestCartItems(): Collection
    {
        $sessionId = Session::getId();

        try {
            // Try database first
            $dbItems = CartItem::forSession($sessionId)
                ->active()
                ->with('productVariant.product')
                ->get();

            if ($dbItems->isNotEmpty()) {
                return $dbItems;
            }

            // Fallback to session
            return $this->getSessionCartItems();

        } catch (\Exception $e) {
            Log::warning('Failed to retrieve guest cart from database, using session fallback', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);

            return $this->getSessionCartItems();
        }
    }

    /**
     * Convert session cart items to consistent format
     */
    private function getSessionCartItems(): Collection
    {
        $sessionCart = Session::get('guest_cart', []);
        
        // Filter out expired items
        $activeItems = collect($sessionCart)->filter(function ($item) {
            return Carbon::parse($item['expires_at'])->isFuture();
        });

        // Update session to remove expired items
        if ($activeItems->count() !== count($sessionCart)) {
            Session::put('guest_cart', $activeItems->values()->all());
        }

        return $activeItems->map(function ($item) {
            $productVariant = ProductVariant::with('product')->find($item['product_variant_id']);
            
            return (object) [
                'id' => $item['id'],
                'user_id' => null,
                'session_id' => Session::getId(),
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'price_when_added' => $item['price_when_added'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'expires_at' => Carbon::parse($item['expires_at']),
                'productVariant' => $productVariant,
                'isSessionBased' => true,
            ];
        });
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(string $itemId, int $quantity): bool
    {
        if (str_starts_with($itemId, 'session_')) {
            return $this->updateSessionItemQuantity($itemId, $quantity);
        }

        $cartItem = $this->findCartItem($itemId);
        if (!$cartItem) return false;

        $cartItem->update(['quantity' => $quantity]);
        return true;
    }

    /**
     * Update session item quantity
     */
    private function updateSessionItemQuantity(string $itemId, int $quantity): bool
    {
        $cart = Session::get('guest_cart', []);
        
        $itemIndex = collect($cart)->search(function ($item) use ($itemId) {
            return $item['id'] === $itemId;
        });

        if ($itemIndex === false) return false;

        $cart[$itemIndex]['quantity'] = $quantity;
        $cart[$itemIndex]['updated_at'] = now()->toISOString();
        
        Session::put('guest_cart', $cart);
        return true;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(string $itemId): bool
    {
        if (str_starts_with($itemId, 'session_')) {
            return $this->removeSessionItem($itemId);
        }

        $cartItem = $this->findCartItem($itemId);
        if (!$cartItem) return false;

        $cartItem->delete();
        return true;
    }

    /**
     * Remove session item
     */
    private function removeSessionItem(string $itemId): bool
    {
        $cart = Session::get('guest_cart', []);
        
        $filteredCart = collect($cart)->reject(function ($item) use ($itemId) {
            return $item['id'] === $itemId;
        });

        Session::put('guest_cart', $filteredCart->values()->all());
        return true;
    }

    /**
     * Find cart item with permission check
     */
    private function findCartItem(string $itemId): ?CartItem
    {
        $cartItem = CartItem::find($itemId);
        if (!$cartItem) return null;

        // Permission check
        if (Auth::check()) {
            if ($cartItem->user_id !== Auth::id()) return null;
        } else {
            if ($cartItem->session_id !== Session::getId()) return null;
        }

        return $cartItem;
    }

    /**
     * Get cart totals
     */
    public function getCartTotals(): array
    {
        $items = $this->getCartItems();
        
        $subtotal = $items->sum(function ($item) {
            return $item->price_when_added * $item->quantity;
        });

        $itemCount = $items->sum('quantity');

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
            'total_items' => $items->count(),
        ];
    }

    /**
     * Merge guest cart when user logs in
     */
    public function mergeGuestCartOnLogin($user): void
    {
        $sessionId = Session::getId();

        try {
            // Merge database guest items
            $this->mergeGuestDatabaseItems($user, $sessionId);
            
            // Merge session items
            $this->mergeGuestSessionItems($user);

        } catch (\Exception $e) {
            Log::error('Failed to merge guest cart on login', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Merge guest database items
     */
    private function mergeGuestDatabaseItems($user, string $sessionId): void
    {
        $guestItems = CartItem::forSession($sessionId)->get();

        foreach ($guestItems as $guestItem) {
            $existingUserItem = CartItem::where('user_id', $user->id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existingUserItem) {
                $existingUserItem->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id' => $user->id,
                    'session_id' => null,
                    'expires_at' => Carbon::now()->addDays(30)
                ]);
            }
        }
    }

    /**
     * Merge guest session items
     */
    private function mergeGuestSessionItems($user): void
    {
        $sessionCart = Session::get('guest_cart', []);

        foreach ($sessionCart as $sessionItem) {
            $this->addItemForUser(
                $user->id,
                $sessionItem['product_variant_id'],
                $sessionItem['quantity'],
                $sessionItem['price_when_added']
            );
        }

        Session::forget('guest_cart');
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): void
    {
        if (Auth::check()) {
            CartItem::forUser(Auth::id())->delete();
        } else {
            $sessionId = Session::getId();
            CartItem::forSession($sessionId)->delete();
            Session::forget('guest_cart');
        }
    }

    /**
     * Get cart count for header display
     */
    public function getCartCount(): int
    {
        return $this->getCartItems()->sum('quantity');
    }
}