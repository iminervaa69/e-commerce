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
                        :shipping="9.99"
                        :tax="($subtotal ?? 0) * 0.08"
                        :discount="0"
                        :total="($subtotal ?? 0) + 9.99 + (($subtotal ?? 0) * 0.08)"
                        :selectedSubtotal="0"
                        :selectedTotal="0"
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
@endsection

@section('insert-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>

    // selected-subtotal
    // Cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeCartHandlers();
        updateSelectedTotals();
    });

    function initializeCartHandlers() {
        // Quantity update handlers
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;
                const input = this.parentElement.querySelector('.quantity-input');
                const itemId = input.dataset.itemId;
                let value = parseInt(input.value);
                
                if (action === 'increase') {
                    value++;
                } else if (action === 'decrease' && value > 1) {
                    value--;
                }
                
                input.value = value;
                updateCartItem(itemId, value, this);
            });
        });

        // Remove item handlers
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                removeCartItem(itemId, this);
            });
        });

        // Individual item checkbox handlers
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateStoreCheckbox(this.dataset.store);
                updateSelectAllCheckbox();
                updateSelectedTotals();
                updateDeleteButton();
            });
        });

        // Store checkbox handlers
        document.querySelectorAll('.store-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const storeName = this.dataset.store;
                const isChecked = this.checked;
                
                // Update all items in this store
                document.querySelectorAll(`.item-checkbox[data-store="${storeName}"]`).forEach(itemCheckbox => {
                    itemCheckbox.checked = isChecked;
                });
                
                updateSelectAllCheckbox();
                updateSelectedTotals();
                updateDeleteButton();
            });
        });

        // Select all items checkbox
        document.getElementById('select-all-items').addEventListener('change', function() {
            const isChecked = this.checked;
            
            // Update all checkboxes
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            document.querySelectorAll('.store-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateSelectedTotals();
            updateDeleteButton();
        });

        // Delete selected button
        document.getElementById('delete-selected').addEventListener('click', function() {
            const selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(checkbox => checkbox.dataset.itemId);
            
            if (selectedItems.length > 0) {
                if (confirm(`Are you sure you want to remove ${selectedItems.length} item(s) from your cart?`)) {
                    deleteSelectedItems(selectedItems);
                }
            }
        });
    }

    function updateStoreCheckbox(storeName) {
        const storeItems = document.querySelectorAll(`.item-checkbox[data-store="${storeName}"]`);
        const checkedStoreItems = document.querySelectorAll(`.item-checkbox[data-store="${storeName}"]:checked`);
        const storeCheckbox = document.querySelector(`.store-checkbox[data-store="${storeName}"]`);
        
        if (storeCheckbox) {
            if (checkedStoreItems.length === 0) {
                storeCheckbox.checked = false;
                storeCheckbox.indeterminate = false;
            } else if (checkedStoreItems.length === storeItems.length) {
                storeCheckbox.checked = true;
                storeCheckbox.indeterminate = false;
            } else {
                storeCheckbox.checked = false;
                storeCheckbox.indeterminate = true;
            }
        }
    }

    function updateSelectAllCheckbox() {
        const allItems = document.querySelectorAll('.item-checkbox');
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        const selectAllCheckbox = document.getElementById('select-all-items');
        
        if (checkedItems.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedItems.length === allItems.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    function updateSelectedTotals() {
        let selectedSubtotal = 0;
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        
        checkedItems.forEach(checkbox => {
            const itemElement = checkbox.closest('.cart-item');
            const priceElement = itemElement.querySelector('[data-item-price]');
            const quantityElement = itemElement.querySelector('.quantity-input');
            
            if (priceElement && quantityElement) {
                const price = parseFloat(priceElement.dataset.itemPrice);
                const quantity = parseInt(quantityElement.value);
                selectedSubtotal += price * quantity;
            }
        });
        
        const shipping = checkedItems.length > 0 ? 9.99 : 0;
        const tax = selectedSubtotal * 0.08;
        const selectedTotal = selectedSubtotal + shipping + tax;
        
        // Update summary via AJAX or direct DOM manipulation
        updateCartSummary(selectedSubtotal, selectedTotal);
    }

    function updateCartSummary(selectedSubtotal, selectedTotal) {
        // Update the selected totals in the summary
        const selectedSubtotalElement = document.getElementById('selected-subtotal');
        const selectedTotalElement = document.getElementById('selected-total');
        
        if (selectedSubtotalElement) {
            selectedSubtotalElement.textContent = `Rp${selectedSubtotal.toFixed(2)}`;
        }
        
        if (selectedTotalElement) {
            selectedTotalElement.textContent = `Rp${selectedTotal.toFixed(2)}`;
        }

        // Show/hide selected totals section
        const selectedSection = document.getElementById('selected-totals-section');
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        
        if (selectedSection) {
            if (checkedItems.length > 0) {
                selectedSection.classList.remove('hidden');
            } else {
                selectedSection.classList.add('hidden');
            }
        }
    }

    function updateDeleteButton() {
        const deleteButton = document.getElementById('delete-selected');
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');
        
        if (deleteButton) {
            deleteButton.disabled = checkedItems.length === 0;
            deleteButton.textContent = checkedItems.length > 0 ? 
                `Delete Selected (${checkedItems.length})` : 'Delete Selected';
        }
    }

    function updateCartItem(itemId, quantity, element) {
        element.disabled = true;
        
        fetch(`/cart/update/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSelectedTotals();
                console.log('Item quantity updated successfully');
            } else {
                console.error('Failed to update quantity:', data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            location.reload();
        })
        .finally(() => {
            element.disabled = false;
        });
    }

    function removeCartItem(itemId, element) {
        element.disabled = true;
        
        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                console.error('Failed to remove item:', data.message);
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
        })
        .finally(() => {
            element.disabled = false;
        });
    }

    function deleteSelectedItems(itemIds) {
        const deleteButton = document.getElementById('delete-selected');
        deleteButton.disabled = true;
        deleteButton.textContent = 'Deleting...';
        
        fetch('/cart/remove-multiple', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ item_ids: itemIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                console.error('Failed to remove items:', data.message);
                alert('Failed to remove selected items. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error removing items:', error);
            alert('An error occurred while removing items. Please try again.');
        })
        .finally(() => {
            deleteButton.disabled = false;
            deleteButton.textContent = 'Delete Selected';
        });
    }
</script>
@endsection