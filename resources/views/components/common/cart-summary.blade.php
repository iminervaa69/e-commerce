{{-- Cart Summary Component --}}
@props([
    'subtotal' => 0.00,
    'shipping' => 0.00,
    'tax' => 0.00,
    'discount' => 0.00,
    'total' => 0.00,
    'selectedSubtotal' => 0.00,
    'selectedTotal' => 0.00
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 sticky top-6">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Order Summary</h2>
        
        <!-- Selected Items Summary (Initially Hidden) -->
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
                    <span id="selected-subtotal">Rp{{ number_format($selectedSubtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-blue-700 dark:text-blue-300">
                    <span>Selected Shipping</span>
                    <span id="selected-shipping">Rp9.99</span>
                </div>
                <div class="flex justify-between text-blue-700 dark:text-blue-300">
                    <span>Selected Tax</span>
                    <span id="selected-tax">Rp0.00</span>
                </div>
                <hr class="border-blue-200 dark:border-blue-600">
                <div class="flex justify-between font-bold text-blue-900 dark:text-blue-100">
                    <span>Selected Total</span>
                    <span id="selected-total">Rp. {{ number_format($selectedTotal, 2) }}</span>
                </div>
            </div>
        </div>
        
        <!-- All Items Summary -->
        <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">All Items Subtotal</span>
                <span class="text-gray-900 dark:text-white">Rp. {{ number_format($subtotal, 2) }}</span>
            </div>
            
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Shipping</span>
                <span>Rp{{ $shipping }}</span>
            </div>
            
            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                <span>Tax</span>
                <span>Rp{{ $tax }}</span>
            </div>
            
            @if(isset($discount) && $discount > 0)
            <div class="flex justify-between text-green-600 dark:text-green-400">
                <span>Discount</span>
                <span>-Rp{{ $discount }}</span>
            </div>
            @endif
            
            <hr class="dark:border-gray-600">
            
            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                <span>Total (All Items)</span>
                <span>Rp{{ $total }}</span>
            </div>
        </div>

        <!-- Promo Code Section -->
        <div class="mb-6">
            <div class="flex gap-2">
                <input type="text" 
                       id="promo-code-input"
                       placeholder="Promo code" 
                       class="flex-1 px-3 py-2 border dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button id="apply-promo-btn" class="px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors font-medium">
                    Apply
                </button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            <!-- Checkout Selected Button -->
            <button id="checkout-selected-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Checkout Selected Items
            </button>

            <!-- Checkout All Button -->
            <button id="checkout-all-btn" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Checkout All Items
            </button>
        </div>

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

    <!-- Additional JavaScript for Summary Component -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize checkout buttons
            updateCheckoutButtons();
            
            // Add event listeners
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('item-checkbox')) {
                    updateCheckoutButtons();
                }
            });

            // Promo code functionality
            document.getElementById('apply-promo-btn').addEventListener('click', function() {
                const promoCode = document.getElementById('promo-code-input').value.trim();
                if (promoCode) {
                    applyPromoCode(promoCode);
                }
            });

            // Checkout button handlers
            document.getElementById('checkout-selected-btn').addEventListener('click', function() {
                const selectedItems = getSelectedItems();
                if (selectedItems.length > 0) {
                    checkoutSelectedItems(selectedItems);
                }
            });

            document.getElementById('checkout-all-btn').addEventListener('click', function() {
                checkoutAllItems();
            });
        });

        function updateCheckoutButtons() {
            const selectedItems = document.querySelectorAll('.item-checkbox:checked');
            const checkoutSelectedBtn = document.getElementById('checkout-selected-btn');
            
            if (checkoutSelectedBtn) {
                checkoutSelectedBtn.disabled = selectedItems.length === 0;
                checkoutSelectedBtn.innerHTML = selectedItems.length > 0 ? 
                    `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Checkout Selected Items (${selectedItems.length})` : 
                    `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Checkout Selected Items`;
            }
        }

        function getSelectedItems() {
            return Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(checkbox => checkbox.dataset.itemId);
        }

        function applyPromoCode(code) {
            const applyBtn = document.getElementById('apply-promo-btn');
            applyBtn.disabled = true;
            applyBtn.textContent = 'Applying...';

            fetch('/cart/apply-promo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ promo_code: code })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload to show updated prices
                } else {
                    alert(data.message || 'Invalid promo code');
                }
            })
            .catch(error => {
                console.error('Error applying promo code:', error);
                alert('Failed to apply promo code');
            })
            .finally(() => {
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';
            });
        }

        function checkoutSelectedItems(itemIds) {
            // Redirect to checkout with selected items
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/checkout';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            const itemsInput = document.createElement('input');
            itemsInput.type = 'hidden';
            itemsInput.name = 'selected_items';
            itemsInput.value = JSON.stringify(itemIds);
            form.appendChild(itemsInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        function checkoutAllItems() {
            // Redirect to checkout with all items
            window.location.href = '/checkout';
        }
    </script>