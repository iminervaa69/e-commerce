@extends('frontend.layouts.main')

@section('title')
Checkout
@endsection

@push('styles')
<script src="https://js.xendit.co/v1/xendit.min.js"></script>
@endpush

@section('content')
<!-- Add this section BEFORE your shipping address section in checkout.blade.php -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Checkout</h1>
            <p class="text-gray-600 dark:text-gray-400">Complete your order</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <!-- Shipping Address Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Shipping Address</h2>
                                <button type="button"
                                        onclick="openAddressModal()"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add New Address
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <x-common.address-selector
                                name="shipping_address"
                                :selected-id="old('shipping_address_id')"
                                api-endpoint="{{ route('addresses.get') }}"
                                empty-title="No shipping addresses"
                                add-button-text="Add Shipping Address"
                                :addresses="$userAddresses"
                                modal-id="address-modal"
                                :show-add-button="true" />
                        </div>
                    </div>

                    <!-- Billing Information Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Billing Information</h2>
                                <button type="button"
                                        onclick="openBillingModal()"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add New Billing Info
                                </button>
                            </div>
                        </div>
                            <div class="p-6">
                                <x-common.billing-selector
                                    name="billing_information"
                                    :selected-id="old('billing_information_id')"
                                    api-endpoint="{{ route('billing.get') }}"
                                    empty-title="No billing information"
                                    add-button-text="Add Billing Information"
                                    :billing-information="$billingInfo ?? []"
                                    modal-id="billing-modal"
                                    :show-add-button="true" />
                            </div>
                    </div>

                    <!-- Payment Method Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Method</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4" x-data="{ paymentMethod: 'card' }">
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
                            <div x-show="paymentMethod === 'card'" x-transition class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <form id="card-form" class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                                            <input type="text" id="card-number" placeholder="1234 5678 9012 3456" value="4000 0000 0000 0002"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                                            <input type="text" id="card-expiry" placeholder="MM/YY" value="12/25"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300" >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                                            <input type="text" id="card-cvv" placeholder="123" value=111
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                                        <input type="text" id="card-name" placeholder="John Doe" value="Rizal"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                    </div>
                                </form>
                            </div>

                            <div x-show="paymentMethod === 'ewallet'" x-transition class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="PH_GCASH">
                                        <div class="text-center">
                                            <div class="w-8 h-8 mx-auto mb-2 bg-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">G</span>
                                            </div>
                                            <div class="text-blue-600 font-semibold">GCash</div>
                                        </div>
                                    </button>
                                    <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="PH_SHOPEEPAY">
                                        <div class="text-center">
                                            <div class="w-8 h-8 mx-auto mb-2 bg-orange-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">S</span>
                                            </div>
                                            <div class="text-orange-600 font-semibold">ShopeePay</div>
                                        </div>
                                    </button>
                                    <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="ID_OVO">
                                        <div class="text-center">
                                            <div class="w-8 h-8 mx-auto mb-2 bg-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">O</span>
                                            </div>
                                            <div class="text-purple-600 font-semibold">OVO</div>
                                        </div>
                                    </button>
                                    <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="ID_DANA">
                                        <div class="text-center">
                                            <div class="w-8 h-8 mx-auto mb-2 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">D</span>
                                            </div>
                                            <div class="text-blue-500 font-semibold">DANA</div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300 sticky top-4">
                    <div class="p-6 border-b dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Summary</h2>
                    </div>
                    <div class="p-6">
                        <x-common.order-summary
                            :items="$selectedItems ?? []"
                            :subtotal="$subtotal ?? 0"
                            :shipping="$shipping ?? 5000"
                            :tax="$tax ?? 0"
                            :discount="$discount ?? 0"
                            :voucher-code="$selectedVoucher['code'] ?? null"
                            checkout-action="handleCheckoutSubmit" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<x-common.address-modal
    modal-id="address-modal"
    :show-default-checkbox="true" />

<!-- Billing Modal -->
<x-common.billing-modal
    modal-id="billing-modal"
    :show-default-checkbox="true" />

<!-- Hidden Payment Form -->
<form id="payment-form" style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id>
    <input type="hidden" name="amount" id="payment-amount">
    <input type="hidden" name="token_id" id="payment-token">
    <input type="hidden" name="authentication_id" id="payment-auth">
    <input type="hidden" name="channel_code" id="payment-channel">
    <input type="hidden" name="shipping_address_id" id="shipping-address-id">
    <input type="hidden" name="billing_information_id" id="billing-information-id">
</form>
@endsection

@push('scripts')
    <script>
    // Xendit configuration
    window.xenditPublicKey = '{{ config("xendivel.public_key") }}';

    if (typeof Xendit !== 'undefined') {
        Xendit.setPublishableKey('{{ config("xendivel.public_key") }}');
    }

    // Bridge function for order summary component
    function handleCheckoutSubmit(component) {
        if (window.checkoutManager) {
            const event = { preventDefault: () => {} };
            window.checkoutManager.handleCheckout(event);
        } else {
            console.error('CheckoutManager not initialized');
            component.isProcessing = false;
        }
    }

    // Payment form logger function
    function logPaymentForm() {
        // console.log('=== PAYMENT FORM VALUES ===');
        
        const paymentForm = document.getElementById('payment-form');
        if (!paymentForm) {
            console.error('Payment form not found!');
            return;
        }
        
        // const inputs = {
        //     token: document.querySelector('input[name="_token"]'),
        //     amount: document.getElementById('payment-amount'),
        //     tokenId: document.getElementById('payment-token'),
        //     authId: document.getElementById('payment-auth'),
        //     channelCode: document.getElementById('payment-channel'),
        //     shippingAddressId: document.getElementById('shipping-address-id'),
        //     billingInfoId: document.getElementById('billing-information-id')
        // };
        
        // Log each input value
        Object.entries(inputs).forEach(([key, input]) => {
            console.log(`${key}:`, input?.value || 'EMPTY/NOT FOUND');
        });
        
        console.log('=== END PAYMENT FORM VALUES ===');
    }

    // Address/Billing refresh functions
    function refreshAddressSelector() {
        console.log('Refreshing address selector...');
        // Implement actual refresh logic if needed
    }

    function refreshBillingSelector() {
        console.log('Refreshing billing selector...');
        // Implement actual refresh logic if needed  
    }

    // Prevent multiple initializations
    let checkoutPageInitialized = false;

    // Main initialization
    document.addEventListener('DOMContentLoaded', function() {
        if (checkoutPageInitialized) {
            console.log('Checkout already initialized, skipping...');
            return;
        }
        
        checkoutPageInitialized = true;
        console.log('Checkout page initialized');
        console.log('Xendit available:', typeof Xendit !== 'undefined');
        
        // Log payment form on load
        // setTimeout(() => logPaymentForm(), 500); // Delay to ensure DOM is ready
        
        // Listen for address/billing selection changes (use event delegation)
        document.addEventListener('change', function(e) {
            if (e.target.name === 'shipping_address' || e.target.name === 'billing_information') {
                console.log('Selection changed:', e.target.name, '=', e.target.value);
            }
        });
        
        // Listen for address events
        ['address-saved', 'address-updated'].forEach(eventType => {
            document.addEventListener(eventType, function(e) {
                console.log(`${eventType}:`, e.detail.address);
                refreshAddressSelector();
            });
        });
        
        // Listen for billing events
        ['billing-saved', 'billing-updated'].forEach(eventType => {
            document.addEventListener(eventType, function(e) {
                console.log(`${eventType}:`, e.detail.billing);
                refreshBillingSelector();
            });
        });
        
        // Watch for payment form changes
        const paymentForm = document.getElementById('payment-form');
        if (paymentForm) {
            paymentForm.querySelectorAll('input').forEach(input => {
                input.addEventListener('change', function() {
                    console.log(`Payment form field changed: ${this.name || this.id} = ${this.value}`);
                });
            });
        }
    });

    // Make functions globally available for debugging
    window.logPaymentForm = logPaymentForm;
    window.logCheckoutData = function() {
        if (window.checkoutManager) {
            window.checkoutManager.logAllCheckoutData();
        }
    };
    </script>
    <script src="{{ asset('js/checkout.js') }}"></script>
    @endpush