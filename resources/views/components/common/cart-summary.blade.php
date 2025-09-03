@props([
'subtotal' => 0.00,
'shipping' => 0.00,
'tax' => 0.00,
'discount' => 0.00,
'total' => 0.00,
'selectedSubtotal' => 0.00,
'selectedTotal' => 0.00,
'selectedVoucher' => null
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 sticky top-6">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Order Summary</h2>

        <div id="selected-totals-section" class="hidden mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Selected Items
            </h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-blue-700 dark:text-blue-300">
                    <span>Selected Subtotal</span>
                    <span id="selected-subtotal">Rp0,00</span>
                </div>
                <div class="flex justify-between text-blue-700 dark:text-blue-300">
                    <span>Selected Shipping</span>
                    <span id="selected-shipping">Rp0,00</span>
                </div>
                <div class="flex justify-between text-blue-700 dark:text-blue-300">
                    <span>Selected Tax</span>
                    <span id="selected-tax">Rp0,00</span>
                </div>
                @if($selectedVoucher)
                <div class="flex justify-between text-green-700 dark:text-green-300">
                    <span>Voucher Discount</span>
                    <span id="selected-discount">-Rp{{ number_format($selectedVoucher['discount_amount'] ?? 0, 2, ',', '.') }}</span>
                </div>
                @endif
                <hr class="border-blue-200 dark:border-blue-600">
                <div class="flex justify-between font-bold text-blue-900 dark:text-blue-100">
                    <span>Selected Total</span>
                    <span id="selected-total">Rp{{ number_format($selectedTotal, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- All Items Summary -->
        <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">All Items Subtotal</span>
                <span class="text-gray-900 dark:text-white">Rp{{ number_format($subtotal, 2, ',', '.') }}</span>
            </div>

            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Shipping</span>
                <span>Rp{{ number_format($shipping, 2, ',', '.') }}</span>
            </div>

            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Tax</span>
                <span>Rp{{ number_format($tax, 2, ',', '.') }}</span>
            </div>

            @if($discount > 0)
            <div class="flex justify-between text-green-600 dark:text-green-400">
                <span>Discount</span>
                <span>-Rp{{ number_format($discount, 2, ',', '.') }}</span>
            </div>
            @endif

            <hr class="dark:border-gray-600">

            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                <span>Total (All Items)</span>
                <span>Rp{{ number_format($total, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Voucher Section -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Voucher & Promo
            </label>

            @if($selectedVoucher)
                <!-- Selected Voucher Display -->
                <div class="flex items-center justify-between p-3 border border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg dark:border-cyan-400">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-md flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-cyan-900 dark:text-cyan-100 truncate">
                                {{ $selectedVoucher['title'] ?? $selectedVoucher['code'] ?? 'Voucher Applied' }}
                            </p>
                            <p class="text-xs text-cyan-700 dark:text-cyan-300">
                                {{ $selectedVoucher['description'] ?? 'Discount applied' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-2">
                        <button
                            type="button"
                            class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-200 text-sm font-medium"
                            onclick="openVoucherDialog()"
                        >
                            Change
                        </button>
                        <button
                            type="button"
                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200"
                            onclick="removeSelectedVoucher()"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @else
                <!-- Voucher Selection Button -->
                <button
                    type="button"
                    class="w-full flex items-center justify-between p-3 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-cyan-500 dark:hover:border-cyan-400 transition-colors"
                    onclick="openVoucherDialog()"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            Select or Enter Voucher
                        </span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif
        </div>

        <div class="space-y-3">
            <button id="checkout-selected-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Checkout Selected Items
            </button>

            <button id="checkout-all-btn" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Checkout All Items
            </button>
        </div>

        <div class="mt-4 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Secure checkout guaranteed</span>
        </div>
    </div>
</div>
