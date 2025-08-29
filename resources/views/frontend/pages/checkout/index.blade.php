@extends('frontend.layouts.main')

@section('title')
Checkout
@endsection

@push('styles')
<script src="https://js.xendit.co/v1/xendit.min.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Checkout</h1>
            <p class="text-gray-600 dark:text-gray-400">Complete your order</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side - Checkout Form -->
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <!-- Shipping Address Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Shipping Address</h2>
                                <button type="button" id="add-new-address" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                    + Add New Address
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <!-- Address Selection Component -->
                            @include('frontend.checkout.components.address-selector')
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Billing Information</h2>
                                <label class="flex items-center text-sm">
                                    <input type="checkbox" id="same-as-shipping" class="text-blue-600 mr-2 rounded">
                                    <span class="text-gray-700 dark:text-gray-300">Same as shipping</span>
                                </label>
                            </div>
                        </div>
                        <div class="p-6">
                            <!-- Billing Information Component -->
                            @include('frontend.checkout.components.billing-selector')
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Method</h2>
                        </div>
                        <div class="p-6">
                            <!-- Payment Method Component -->
                            @include('frontend.checkout.components.payment-method')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300 sticky top-4">
                    <div class="p-6 border-b dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Summary</h2>
                    </div>
                    <div class="p-6">
                        <!-- Order Items -->
                        <div class="space-y-4 mb-6">
                            @if(isset($selectedItems) && $selectedItems->count() > 0)
                                @foreach($selectedItems as $item)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($item->productVariant->image ?? $item->productVariant->product->featured_image)
                                            <img src="{{ asset('storage/' . ($item->productVariant->image ?? $item->productVariant->product->featured_image)) }}"
                                                 alt="{{ $item->productVariant->product->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $item->productVariant->product->name }}
                                        </h3>
                                        @if($item->productVariant->attributes && count($item->productVariant->attributes) > 0)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                @foreach($item->productVariant->attributes as $attr => $value)
                                                    {{ ucfirst($attr) }}: {{ $value }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </p>
                                        @endif
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Qty: {{ $item->quantity }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Store: {{ $item->productVariant->product->store->name ?? 'Unknown Store' }}
                                        </p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        @if($item->productVariant->compare_at_price && $item->productVariant->compare_at_price > $item->price_when_added)
                                            <p class="text-xs text-gray-400 line-through">
                                                Rp{{ number_format($item->productVariant->compare_at_price, 0, ',', '.') }}
                                            </p>
                                        @endif
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            Rp{{ number_format($item->price_when_added * $item->quantity, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Rp{{ number_format($item->price_when_added, 0, ',', '.') }} each
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No items to checkout</p>
                                </div>
                            @endif
                        </div>

                        <!-- Summary -->
                        <div class="border-t dark:border-gray-700 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-white" id="subtotal">Rp{{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                <span class="text-gray-900 dark:text-white">Rp{{ number_format($shipping ?? 5000, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-white">Rp{{ number_format($tax ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if(isset($discount) && $discount > 0)
                            <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                                <span>Discount
                                    @if(isset($selectedVoucher))
                                        <span class="text-xs">({{ $selectedVoucher['code'] ?? 'Voucher' }})</span>
                                    @endif
                                </span>
                                <span>-Rp{{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t dark:border-gray-700 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white" id="total">Rp{{ number_format($total ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <button id="checkout-btn" class="w-full mt-6 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="btn-text">Complete Order</span>
                            <svg id="btn-loading" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
@include('frontend.checkout.components.address-modal')

<!-- Hidden form for payment processing -->
<form id="payment-form" style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="amount" id="payment-amount">
    <input type="hidden" name="token_id" id="payment-token">
    <input type="hidden" name="authentication_id" id="payment-auth">
    <input type="hidden" name="channel_code" id="payment-channel">
    <input type="hidden" name="shipping_address_id" id="shipping-address-id">
    <input type="hidden" name="billing_address_id" id="billing-address-id">
</form>
@endsection

@push('scripts')
<script>
// Set Xendit public key globally for checkout.js to use
window.xenditPublicKey = '{{ config("xendivel.public_key") }}';

// Initialize Xendit directly (backup in case checkout.js doesn't load)
if (typeof Xendit !== 'undefined') {
    Xendit.setPublishableKey('{{ config("xendivel.public_key") }}');
}

// Basic initialization check
document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page initialized');
    console.log('Xendit available:', typeof Xendit !== 'undefined');
    console.log('CheckoutManager available:', typeof CheckoutManager !== 'undefined');
});
</script>
<script src="{{ asset('js/checkout.js') }}"></script>
<script src="{{ asset('js/checkout-components.js') }}"></script>
@endpush
