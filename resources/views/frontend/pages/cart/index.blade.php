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
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Cart Items (<span id="cart-item-count">{{ $itemCount ?? 0 }}</span>)
                            </h2>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" id="select-all-items" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    Select All Items
                                </label>
                                <button id="delete-selected" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="cart-items-container">
                        @php
                            $groupedItems = $cartItems->groupBy(function($item) {
                                return $item->productVariant->product->store->name ?? 'Unknown Store';
                            });
                        @endphp

                        @foreach($groupedItems as $storeName => $storeItems)
                        <div class="store-group" data-store="{{ $storeName }}">
                            <!-- Store Header -->
                            <div class="store-header px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" class="store-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" data-store="{{ $storeName }}">
                                            <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $storeName }}</span>
                                        </label>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $storeItems->count() }} items)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2m13-16H7a2 2 0 012-2h6a2 2 0 012 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Store Items -->
                            <div class="store-items divide-y dark:divide-gray-700">
                                @foreach($storeItems as $item)
                                <div class="cart-item" data-item-id="{{ $item->id }}" data-store="{{ $storeName }}">
                                    <x-common.cart-item 
                                        :id="$item->id"
                                        :name="$item->productVariant->product->name"
                                        :price="$item->price_when_added"
                                        :originalPrice="$item->productVariant->compare_at_price"
                                        :quantity="$item->quantity"
                                        :image="$item->productVariant->image ?? $item->productVariant->product->featured_image"
                                        :inStock="$item->productVariant->stock_quantity > 0"
                                        :productAttributes="$item->productVariant->attributes ?? []"
                                        :storeName="$storeName"
                                    />
                                </div>
                                @endforeach
                            </div>
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
                        :shipping="10000"
                        :tax="($subtotal ?? 0) * 0.01"
                        :discount="0"
                        :total="($subtotal ?? 0) + 10000 + (($subtotal ?? 0) * 0.01)"
                        :selectedSubtotal="0"
                        :selectedTotal="0"
                        :selectedVoucher="$selectedVoucher ?? null"
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
<x-common.voucher-dialog 
    :vouchers="$availableVouchers ?? []"
    :selectedVoucher="$selectedVoucher ?? null"
    :isOpen="true"
    dialogId="cartVoucherDialog"
/>
@endsection

@section('insert-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{ asset('js/cart-manager.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dialog = document.getElementById('cartVoucherDialog');
    if (dialog) {
        dialog.classList.add('hidden');
    }
});
</script>
@endsection