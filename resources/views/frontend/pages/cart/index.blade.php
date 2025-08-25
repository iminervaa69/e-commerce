@extends('frontend.layouts.main')

@section('title')
Cart
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Shopping Cart</h1>
            <p class="text-gray-600 dark:text-gray-400">Review your items before checkout</p>
        </div>

        @if(isset($cartItems) && $cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700">
                    <div class="p-6 border-b dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Cart Items (<span id="cart-item-count">{{ $itemCount ?? 0 }}</span>)
                        </h2>
                    </div>
                    
                    <div class="divide-y dark:divide-gray-700" id="cart-items-container">
                        @foreach(($cartItems ?? collect()) as $item)
                        <div class="cart-item" data-item-id="{{ $item->id }}">
                            <x-common.cart-item 
                                :id="$item->id"
                                :name="$item->productVariant->product->name"
                                :price="$item->price_when_added"
                                :originalPrice="$item->productVariant->compare_at_price"
                                :quantity="$item->quantity"
                                :image="$item->productVariant->image ?? $item->productVariant->product->featured_image"
                                :inStock="$item->productVariant->stock_quantity > 0"
                                :productAttributes="$item->productVariant->attributes ?? []"
                            />
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div id="cart-summary-container">
                    <x-common.cart-summary 
                        :subtotal="$subtotal ?? 0"
                        :shipping="9.99"
                        :tax="($subtotal ?? 0) * 0.08"
                        :discount="0"
                        :total="($subtotal ?? 0) + 9.99 + (($subtotal ?? 0) * 0.08)"
                    />
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 p-12">
                <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Start shopping to add items to your cart</p>
                <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Start Shopping
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('insert-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    // Cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity update handlers
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;
                const input = this.parentElement.querySelector('.quantity-input');
                const itemId = input.dataset.itemId;
                let value = parseInt(input.value);
                
                if (action === 'increase') {
                    value++;
                } else if (action === 'decrease' && value > 1) {
                    value--;
                }
                
                input.value = value;
                updateCartItem(itemId, value, this);
            });
        });

        // Remove item handlers
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                removeCartItem(itemId, this);
            });
        });
    });

    function updateCartItem(itemId, quantity, element) {
        element.disabled = true;
        
        fetch(`/cart/update/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Item quantity updated successfully');
            } else {
                console.error('Failed to update quantity:', data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            location.reload();
        })
        .finally(() => {
            element.disabled = false;
        });
    }

    function removeCartItem(itemId, element) {
        element.disabled = true;
        
        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Simple approach for now
            } else {
                console.error('Failed to remove item:', data.message);
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
        })
        .finally(() => {
            element.disabled = false;
        });
    }
</script>
@endsection