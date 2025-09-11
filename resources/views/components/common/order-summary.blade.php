{{-- resources/views/components/order-summary.blade.php --}}
@props([
    'items' => [],
    'subtotal' => 0,
    'shipping' => 5000,
    'tax' => 0,
    'discount' => 0,
    'voucherCode' => null,
    'isLoading' => false,
    'showCheckoutButton' => true,
    'checkoutText' => 'Complete Order',
    'processingText' => 'Processing...',
    'emptyTitle' => 'Your cart is empty',
    'emptyDescription' => 'Add some items to checkout',
    'loadingText' => 'Loading order...',
    'checkoutAction' => null
])

@php
// Handle both arrays and collections
$itemsCollection = is_array($items) ? collect($items) : $items;

// Calculate total using the passed subtotal or calculate from items
if ($subtotal > 0) {
    // Use passed subtotal from controller
    $calculatedSubtotal = $subtotal;
} else {
    // Fallback calculation for items with productVariant relationship (legacy support)
    $calculatedSubtotal = $itemsCollection->sum(function($item) {
        if (isset($item['price']) && isset($item['quantity'])) {
            // Controller data structure: uses 'price' key
            return $item['price'] * $item['quantity'];
        } else {
            // Legacy data structure: uses productVariant relationship
            return ($item->price_when_added ?? $item->productVariant->price) * $item->quantity;
        }
    });
}

$calculatedTotal = $calculatedSubtotal + $shipping + $tax - $discount;
@endphp

<div x-data="{
        isProcessing: false,
        handleCheckout() {
            if (this.isProcessing) return;

            this.isProcessing = true;

            @if($checkoutAction)
                // Call custom checkout action
                {{ $checkoutAction }}(this);
            @else
                // Default checkout behavior
                setTimeout(() => {
                    this.isProcessing = false;
                    alert('Checkout functionality not implemented');
                }, 2000);
            @endif
        }
    }"
     class="order-summary">

    <!-- Loading State -->
    @if($isLoading)
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">{{ $loadingText }}</span>
        </div>
    @else
        <!-- Order Items -->
        <div class="space-y-4 mb-6">
            @if($itemsCollection && $itemsCollection->count() > 0)
                @foreach($itemsCollection as $item)
                <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg overflow-hidden flex-shrink-0">
                        @php
                            // Handle different data structures for image
                            $image = null;
                            if (is_array($item) || is_object($item)) {
                                $image = $item['image'] ?? $item->image ?? null;
                            }

                            // Legacy support for productVariant relationship
                            if (!$image && isset($item->productVariant)) {
                                $image = $item->productVariant->image ?? $item->productVariant->product->featured_image ?? null;
                            }
                        @endphp

                        @if($image)
                            <img src="{{ asset('storage/' . $image) }}"
                                 alt="{{ is_array($item) ? $item['name'] : ($item->name ?? $item->productVariant->product->name) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-gray-900 dark:text-white truncate">
                            @if(is_array($item))
                                {{ $item['name'] }}
                            @else
                                {{ $item->name ?? $item->productVariant->product->name }}
                            @endif
                        </h3>

                        @php
                            // Handle variant attributes for both data structures
                            $variantAttributes = null;
                            if (is_array($item)) {
                                $variantAttributes = $item['variant_attributes'] ?? null;
                                $variantName = $item['variant_name'] ?? null;
                            } else {
                                $variantAttributes = $item->productVariant->attributes ?? null;
                                $variantName = $item->productVariant->name ?? null;
                            }
                        @endphp

                        @if($variantName)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $variantName }}</p>
                        @elseif($variantAttributes && is_array($variantAttributes) && count($variantAttributes) > 0)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @foreach($variantAttributes as $attr => $value)
                                    {{ ucfirst($attr) }}: {{ $value }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        @endif

                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Qty: {{ is_array($item) ? $item['quantity'] : $item->quantity }}
                        </p>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Store:
                            @if(is_array($item))
                                {{ $item['store_name'] ?? 'Unknown Store' }}
                            @else
                                {{ $item->productVariant->product->store->name ?? 'Unknown Store' }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @php
                            // Calculate item price and total for both data structures
                            if (is_array($item)) {
                                $itemPrice = $item['price'];
                                $itemQuantity = $item['quantity'];
                                $itemTotal = $item['total'] ?? ($itemPrice * $itemQuantity);
                                $currentPrice = $item['current_price'] ?? $itemPrice;
                            } else {
                                $itemPrice = $item->price_when_added ?? $item->productVariant->price;
                                $itemQuantity = $item->quantity;
                                $itemTotal = $itemPrice * $itemQuantity;
                                $currentPrice = $item->productVariant->price;
                            }

                            // Handle compare at price (for legacy support)
                            $compareAtPrice = null;
                            if (!is_array($item) && isset($item->productVariant->compare_at_price)) {
                                $compareAtPrice = $item->productVariant->compare_at_price;
                            }
                        @endphp

                        @if($compareAtPrice && $compareAtPrice > $itemPrice)
                            <p class="text-xs text-gray-400 line-through">
                                Rp{{ number_format($compareAtPrice, 0, ',', '.') }}
                            </p>
                        @endif

                        <p class="font-medium text-gray-900 dark:text-white">
                            Rp{{ number_format($itemTotal, 0, ',', '.') }}
                        </p>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Rp{{ number_format($itemPrice, 0, ',', '.') }} each
                        </p>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $emptyTitle }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ $emptyDescription }}</p>
                </div>
            @endif
        </div>

        <!-- Summary Calculations -->
        @if($itemsCollection && $itemsCollection->count() > 0)
            <div class="border-t dark:border-gray-700 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="text-gray-900 dark:text-white" data-subtotal="{{ $calculatedSubtotal }}">
                        Rp{{ number_format($calculatedSubtotal, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                    <span class="text-gray-900 dark:text-white" data-shipping="{{ $shipping }}">
                        Rp{{ number_format($shipping, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Tax</span>
                    <span class="text-gray-900 dark:text-white" data-tax="{{ $tax }}">
                        Rp{{ number_format($tax, 0, ',', '.') }}
                    </span>
                </div>
                @if($discount > 0)
                    <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                        <span>Discount
                            @if($voucherCode)
                                <span class="text-xs">({{ $voucherCode }})</span>
                            @endif
                        </span>
                        <span data-discount="{{ $discount }}">-Rp{{ number_format($discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="border-t dark:border-gray-700 pt-2">
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white" data-total="{{ $calculatedTotal }}">
                            Rp{{ number_format($calculatedTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Checkout Button -->
            @if($showCheckoutButton)
                <button x-on:click="handleCheckout()"
                        class="w-full mt-6 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                        x-bind:disabled="isProcessing">
                    <template x-if="!isProcessing">
                        <span>{{ $checkoutText }}</span>
                    </template>
                    <template x-if="isProcessing">
                        <div class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ $processingText }}</span>
                        </div>
                    </template>
                </button>
            @endif
        @endif
    @endif
</div>

@push('scripts')
<script>
// Listen for address/billing changes and reload totals
document.addEventListener('DOMContentLoaded', function() {
    // Listen for shipping address changes
    const shippingInputs = document.querySelectorAll('input[name="shipping_address"]');
    shippingInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (window.checkoutManager) {
                window.checkoutManager.loadCheckoutTotals();
                console.log('Checkout totals reloaded after shipping address change');
            }
        });
    });

    // Listen for billing information changes
    const billingInputs = document.querySelectorAll('input[name="billing_information"]');
    billingInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (window.checkoutManager) {
                window.checkoutManager.loadCheckoutTotals();
                console.log('Checkout totals reloaded after billing change');
            }
        });
    });
});
</script>
@endpush
