{{-- Cart Summary Component --}}
@props([
    'subtotal' => 0.00,
    'shipping' => 0.00,
    'tax' => 0.00,
    'discount' => 0.00,
    'total' => 0.00
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 sticky top-6">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Order Summary</h2>
        
        <!-- Summary Items -->
        <div class="space-y-3 mb-6">
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>
            
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Shipping</span>
                <span>${{ number_format($shipping, 2) }}</span>
            </div>
            
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Tax</span>
                <span>${{ number_format($tax, 2) }}</span>
            </div>
            
            @if(isset($discount) && $discount > 0)
            <div class="flex justify-between text-green-600 dark:text-green-400">
                <span>Discount</span>
                <span>-${{ number_format($discount, 2) }}</span>
            </div>
            @endif
            
            <hr class="dark:border-gray-600">
            
            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                <span>Total</span>
                <span>${{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- Promo Code Section -->
        <div class="mb-6">
            <div class="flex gap-2">
                <input type="text" 
                       placeholder="Promo code" 
                       class="flex-1 px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button class="px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors font-medium">
                    Apply
                </button>
            </div>
        </div>

        <!-- Checkout Button -->
        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Proceed to Checkout
        </button>

        <!-- Security Badge -->
        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Secure checkout guaranteed</span>
        </div>

        <!-- Payment Methods -->
        <div class="mt-6 pt-4 border-t dark:border-gray-600">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">We accept:</p>
            <div class="flex justify-center gap-2 opacity-60">
                <div class="w-8 h-6 bg-blue-600 rounded text-white text-xs flex items-center justify-center font-bold">V</div>
                <div class="w-8 h-6 bg-red-600 rounded text-white text-xs flex items-center justify-center font-bold">MC</div>
                <div class="w-8 h-6 bg-blue-500 rounded text-white text-xs flex items-center justify-center font-bold">AE</div>
                <div class="w-8 h-6 bg-purple-600 rounded text-white text-xs flex items-center justify-center font-bold">PP</div>
            </div>
        </div>

        <!-- Estimated Delivery -->
        <div class="mt-6 pt-4 border-t dark:border-gray-600">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Estimated delivery: {{ date('M d, Y', strtotime('+5 days')) }}</span>
            </div>
        </div>
    </div>
</div>