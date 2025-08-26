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
                    <!-- Payment Method Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Method</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4" x-data="{ paymentMethod: 'card' }">
                                <!-- Credit/Debit Card -->
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300"
                                       :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="text-blue-600">
                                    <div class="ml-3">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                                            </svg>
                                            <span class="font-medium text-gray-900 dark:text-white">Credit/Debit Card</span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Visa, Mastercard, etc.</p>
                                    </div>
                                </label>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token">
                                
                                <!-- E-Wallet -->
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300"
                                       :class="paymentMethod === 'ewallet' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" name="payment_method" value="ewallet" x-model="paymentMethod" class="text-blue-600">
                                    <div class="ml-3">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                            </svg>
                                            <span class="font-medium text-gray-900 dark:text-white">E-Wallet</span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">GCash, ShopeePay, GrabPay, etc.</p>
                                    </div>
                                </label>

                                <!-- Card Form -->
                                <div x-show="paymentMethod === 'card'" x-transition class="mt-6">
                                    <form id="card-form" class="space-y-4">
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                                                <input type="text" id="card-number" placeholder="1234 5678 9012 3456" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                                                <input type="text" id="card-expiry" placeholder="MM/YY" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                                                <input type="text" id="card-cvv" placeholder="123" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                                            <input type="text" id="card-name" placeholder="John Doe" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                        </div>
                                    </form>
                                </div>

                                <!-- E-Wallet Selection -->
                                <div x-show="paymentMethod === 'ewallet'" x-transition class="mt-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500" data-channel="PH_GCASH">
                                            <div class="text-center">
                                                <div class="text-blue-600 font-semibold">GCash</div>
                                            </div>
                                        </button>
                                        <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500" data-channel="PH_SHOPEEPAY">
                                            <div class="text-center">
                                                <div class="text-orange-600 font-semibold">ShopeePay</div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Billing Information</h2>
                        </div>
                        <div class="p-6">
                            <form id="billing-form" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                                        <input type="text" name="first_name" 
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                                        <input type="text" name="last_name" 
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <input type="email" name="email" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                </div>
                            </form>
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
                            <!-- Sample items - replace with your cart data -->
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 dark:text-white">Sample Product</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Qty: 1</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 dark:text-white">$99.00</p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="border-t dark:border-gray-700 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-white" id="subtotal">$99.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                <span class="text-gray-900 dark:text-white">$9.99</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-white">$8.72</span>
                            </div>
                            <div class="border-t dark:border-gray-700 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white" id="total">$117.71</span>
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

<!-- Hidden form for payment processing -->
<form id="payment-form" style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="amount" id="payment-amount">
    <input type="hidden" name="token_id" id="payment-token">
    <input type="hidden" name="authentication_id" id="payment-auth">
    <input type="hidden" name="channel_code" id="payment-channel">
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
@endpush