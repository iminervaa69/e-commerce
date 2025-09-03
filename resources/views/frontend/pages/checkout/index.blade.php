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
                                <label class="flex items-center text-sm cursor-pointer">
                                    <input type="checkbox"
                                           id="same-as-shipping"
                                           class="text-blue-600 mr-2 rounded focus:ring-blue-500"
                                           onchange="toggleBillingAddress(this)">
                                    <span class="text-gray-700 dark:text-gray-300">Same as shipping</span>
                                </label>
                            </div>
                        </div>
                        <div class="p-6" id="billing-address-section">
                        </div>
                    </div>

                    <!-- Payment Method Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Method</h2>
                        </div>
                        <div class="p-6">
                            <x-common.payment-method/>
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

<!-- Hidden Payment Form -->
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

// Function to toggle billing address section
function toggleBillingAddress(checkbox) {
    const billingSection = document.getElementById('billing-address-section');
    const shippingAddressInputs = document.querySelectorAll('input[name="shipping_address"]:checked');

    if (checkbox.checked) {
        // Hide billing address section and copy shipping address
        billingSection.style.opacity = '0.5';
        billingSection.style.pointerEvents = 'none';

        // Copy shipping address value to billing
        if (shippingAddressInputs.length > 0) {
            const shippingValue = shippingAddressInputs[0].value;
            const billingInput = document.querySelector(`input[name="billing_address"][value="${shippingValue}"]`);
            if (billingInput) {
                billingInput.checked = true;
            }
        }

        console.log('Billing same as shipping enabled');
    } else {
        // Show billing address section
        billingSection.style.opacity = '1';
        billingSection.style.pointerEvents = 'auto';

        console.log('Billing same as shipping disabled');
    }
}

// Custom checkout handler
function handleCheckoutSubmit(component) {
    console.log('Starting checkout process...');

    // Get selected addresses
    const shippingAddress = document.querySelector('input[name="shipping_address"]:checked')?.value;
    const billingAddress = document.querySelector('input[name="billing_address"]:checked')?.value;
    const sameAsShipping = document.getElementById('same-as-shipping')?.checked;

    // Validation
    if (!shippingAddress) {
        alert('Please select a shipping address');
        component.isProcessing = false;
        return;
    }

    if (!sameAsShipping && !billingAddress) {
        alert('Please select a billing address');
        component.isProcessing = false;
        return;
    }

    // Set form values
    document.getElementById('shipping-address-id').value = shippingAddress;
    document.getElementById('billing-address-id').value = sameAsShipping ? shippingAddress : billingAddress;

    // Simulate checkout process
    setTimeout(() => {
        component.isProcessing = false;

        // Here you would normally:
        // 1. Process payment with Xendit
        // 2. Submit the form to your backend
        // 3. Redirect to success page

        alert(`Checkout completed!\nShipping: ${shippingAddress}\nBilling: ${sameAsShipping ? shippingAddress : billingAddress}`);

        // Example: Submit to backend
        // document.getElementById('payment-form').submit();

        // Example: Redirect to success
        // window.location.href = '/checkout/success';

    }, 2000);

    // Refresh totals periodically
    setInterval(function() {
        fetch('/checkout/refresh-totals', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update displayed totals
                updateCheckoutDisplay(data.data);
            }
        });
    }, 300000); // Refresh every 5 minutes
}

// Basic initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page initialized');
    console.log('Xendit available:', typeof Xendit !== 'undefined');
    console.log('AddressManager available:', typeof AddressManager !== 'undefined');

    // Listen for address selection changes
    document.addEventListener('change', function(e) {
        if (e.target.name === 'shipping_address' || e.target.name === 'billing_address') {
            console.log('Address selection changed:', e.target.name, e.target.value);

            // Auto-sync billing address if "same as shipping" is checked
            if (e.target.name === 'shipping_address' && document.getElementById('same-as-shipping')?.checked) {
                const billingInput = document.querySelector(`input[name="billing_address"][value="${e.target.value}"]`);
                if (billingInput) {
                    billingInput.checked = true;
                }
            }
        }
    });
});
</script>
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush
