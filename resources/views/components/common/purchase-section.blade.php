    @props([
        'stock' => 10,
        'price' => 0,
        'minOrder' => 1,
        'maxOrder' => null,
        'productId' => null,
        'productVariantId' => null,
        'showNotes' => false,
        'buttonText' => '+ Keranjang',
        'secondaryButtonText' => 'Beli Langsung'
    ])

    <div class="bg-white dark:bg-gray-800 rounded-md border p-4 dark:border-gray-700 sticky top-4">
        <div class="space-y-4">
            <h3 class="font-semibold text-lg dark:text-white">Atur jumlah dan catatan</h3>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center border border-gray-400 dark:border-gray-600 rounded-md">
                    <button 
                        type="button" 
                        class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-transparent dark:text-white"
                        onclick="decreaseQuantity()"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus-icon lucide-minus items-center"><path d="M5 12h14"/></svg>
                    </button>
                    <input 
                        type="number" 
                        id="quantity" 
                        value="{{ $minOrder }}" 
                        min="{{ $minOrder }}"
                        @if($maxOrder) max="{{ $maxOrder }}" @endif
                        class="w-16 text-center border-0 focus:ring-0 bg-transparent dark:text-white -webkit-appearance: none;"
                        onchange="updateTotal()"
                    >
                    <button 
                        type="button" 
                        class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-transparent dark:text-white"
                        onclick="increaseQuantity()"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    </button>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-600 dark:text-gray-200">Stok Total: </span>
                    <span class="text-sm font-medium text-orange-500">Sisa {{ $stock }}</span>
                </div>
            </div>

            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600 dark:text-gray-200">Subtotal</span>
                <span class="font-bold text-xl dark:text-white" id="subtotal">{{ $price }}</span>
            </div>

            @if($showNotes)
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-white">Catatan untuk penjual (opsional)</label>
                    <textarea 
                        id="notes"
                        rows="3" 
                        class="w-full border rounded-md p-2 text-xs border-gray-400 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:bg-transparent dark:text-white"
                        placeholder="Tulis catatan..."
                    ></textarea>
                </div>
            @endif

            <div class="space-y-3">
                <button 
                    type="button"
                    id="addToCartBtn"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white py-3 px-4 rounded-md font-medium transition-colors"
                    onclick="addToCart()"
                >
                    {{ $buttonText }}
                </button>
                
                <button 
                    type="button"
                    class="w-full border border-cyan-600 text-cyan-600 hover:bg-cyan-600 hover:text-white py-3 px-4 rounded-md font-medium transition-colors"
                    onclick="buyNow()"
                >
                    {{ $secondaryButtonText }}
                </button>
            </div>

            <div class="flex justify-between pt-4">
                <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 dark:text-gray-200 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24 " stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/><path d="M12 11h.01"/><path d="M16 11h.01"/><path d="M8 11h.01"/>
                    </svg>
                    <span class="text-sm">Chat</span>
                </button>
                
                <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 dark:text-gray-200 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path  d="m14.479 19.374-.971.939a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5a5.2 5.2 0 0 1-.219 1.49"/><path d="M15 15h6"/><path d="M18 12v6"/>
                    </svg>
                    <span class="text-sm">Wishlist</span>
                </button>
                
                <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 dark:text-gray-200 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                    </svg>
                    <span class="text-sm">Share</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    const bPrice = {{ $price }};
    const maxStock = {{ $stock }};
    const minOrderQty = {{ $minOrder }};
    const productVariantId = {{ $productVariantId ?? 'null' }};

    function updateQuantity() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const total = bPrice * quantity;
        document.getElementById('subtotal').textContent = 'Rp' + total.toLocaleString('id-ID');
    }

    function increaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        console.log('Current value:', currentValue, 'Max stock:', maxStock);
        if (currentValue < maxStock) {
            quantityInput.value = currentValue + 1;
            updateQuantity();
        }
    }

    function decreaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > minOrderQty) {
            quantityInput.value = currentValue - 1;
            updateQuantity();
        }
    }

    function updateTotal() {
        updateQuantity();
    }

    function addToCart() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const notes = document.getElementById('notes')?.value || '';
        const button = document.getElementById('addToCartBtn');
        
        if (!productVariantId) {
            // showNotification('Product variant ID is required', 'error');
            // return;
        }
        
        // Disable button during request
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = 'Adding...';
        
        // Send AJAX request to add item to cart
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_variant_id: productVariantId,
                quantity: quantity,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification(data.message, 'success');
                
                // Update cart count in header if you have one
                updateCartCount(data.cart_count);
                
                // Temporarily change button text to show success
                button.textContent = 'Added!';
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                button.classList.remove('bg-cyan-500', 'hover:bg-cyan-600');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-cyan-500', 'hover:bg-cyan-600');
                    button.disabled = false;
                }, 2000);
                
            } else {
                showNotification(data.message || 'Failed to add item to cart', 'error');
                button.textContent = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    function buyNow() {
        const quantity = document.getElementById('quantity').value;
        const notes = document.getElementById('notes')?.value || '';
        console.log('Buy now:', quantity, 'Notes:', notes);
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    function updateCartCount(count) {
        const cartCountElement = document.querySelector('[data-cart-count]');
        if (cartCountElement) {
            cartCountElement.textContent = count;
            cartCountElement.classList.add('animate-pulse');
            setTimeout(() => {
                cartCountElement.classList.remove('animate-pulse');
            }, 1000);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateQuantity();
    });
    </script>