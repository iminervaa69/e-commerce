// Cart Management System - Centralized
document.addEventListener('DOMContentLoaded', function() {
    initializeCartSystem();
});

// ===== UTILITY FUNCTIONS =====
function formatIndonesianPrice(amount) {
    return 'Rp' + new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// ===== PRICE DISPLAY FUNCTIONS =====
function updateItemPriceDisplay(itemElement) {
    const quantityInput = itemElement.querySelector('.quantity-input');
    const totalPriceElement = itemElement.querySelector('.total-price');
    const unitPriceDisplay = itemElement.querySelector('.unit-price-display');
    const pricePerUnit = parseFloat(itemElement.querySelector('[data-item-price]').dataset.itemPrice);
    
    if (!quantityInput || !totalPriceElement || !unitPriceDisplay) return;
    
    const quantity = parseInt(quantityInput.value) || 1;
    const totalPrice = pricePerUnit * quantity;
    
    // Update total price
    totalPriceElement.textContent = formatIndonesianPrice(totalPrice);
    
    // Show/hide unit price display
    if (quantity > 1) {
        unitPriceDisplay.style.display = '';
        unitPriceDisplay.textContent = formatIndonesianPrice(pricePerUnit) + ' each';
    } else {
        unitPriceDisplay.style.display = 'none';
    }
}

function updateAllItemPrices() {
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach(item => {
        updateItemPriceDisplay(item);
    });
}

// ===== CHECKBOX MANAGEMENT =====
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
    
    if (selectAllCheckbox) {
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
}

// ===== TOTALS CALCULATION =====
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
    
    const shipping = checkedItems.length > 0 ? 10000 : 0;
    const tax = selectedSubtotal * 0.01;
    const selectedTotal = selectedSubtotal + shipping + tax;
    
    updateCartSummary(selectedSubtotal, selectedTotal);
}

function updateCartSummary(selectedSubtotal, selectedTotal) {
    // Update selected totals
    const selectedSubtotalElement = document.getElementById('selected-subtotal');
    const selectedTotalElement = document.getElementById('selected-total');
    const selectedShippingElement = document.getElementById('selected-shipping');
    const selectedTaxElement = document.getElementById('selected-tax');
    
    if (selectedSubtotalElement) {
        selectedSubtotalElement.textContent = formatIndonesianPrice(selectedSubtotal);
    }
    
    if (selectedShippingElement) {
        const shippingAmount = selectedSubtotal > 0 ? 10000 : 0;
        selectedShippingElement.textContent = formatIndonesianPrice(shippingAmount);
    }
    
    if (selectedTaxElement) {
        selectedTaxElement.textContent = formatIndonesianPrice(selectedSubtotal * 0.01);
    }
    
    if (selectedTotalElement) {
        selectedTotalElement.textContent = formatIndonesianPrice(selectedTotal);
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

// ===== BUTTON UPDATES =====
function updateDeleteButton() {
    const deleteButton = document.getElementById('delete-selected');
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    
    if (deleteButton) {
        deleteButton.disabled = checkedItems.length === 0;
        deleteButton.textContent = checkedItems.length > 0 ? 
            `Delete Selected (${checkedItems.length})` : 'Delete Selected';
    }
}

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

// ===== QUANTITY MANAGEMENT =====
function handleQuantityChange(button) {
    const cartItem = button.closest('.cart-item');
    const quantityInput = cartItem.querySelector('.quantity-input');
    const itemId = button.dataset.itemId;
    const action = button.dataset.action;
    
    let newQuantity = parseInt(quantityInput.value) || 1;
    
    if (action === 'increase' && newQuantity < 99) {
        newQuantity++;
    } else if (action === 'decrease' && newQuantity > 1) {
        newQuantity--;
    }
    
    quantityInput.value = newQuantity;
    
    // Update button states
    const increaseBtn = cartItem.querySelector('.increase-btn');
    const decreaseBtn = cartItem.querySelector('.decrease-btn');
    increaseBtn.disabled = newQuantity >= 99;
    decreaseBtn.disabled = newQuantity <= 1;
    
    // Update price display
    updateItemPriceDisplay(cartItem);
    
    // Update totals
    updateSelectedTotals();
    
    // Make API call
    updateCartItemOnServer(itemId, newQuantity, button);
}

function updateCartItemOnServer(itemId, quantity, element) {
    // Show loading
    const loadingOverlay = element.closest('.cart-item').querySelector('.quantity-loading');
    if (loadingOverlay) loadingOverlay.classList.remove('hidden');
    
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
        if (!data.success) {
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
        if (loadingOverlay) loadingOverlay.classList.add('hidden');
    });
}

// ===== CART ACTIONS =====
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
            showNotification('Failed to remove selected items. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing items:', error);
        showNotification('An error occurred while removing items. Please try again.', 'error');
    })
    .finally(() => {
        deleteButton.disabled = false;
        deleteButton.textContent = 'Delete Selected';
    });
}

// ===== CHECKOUT FUNCTIONS =====
function getSelectedItems() {
    return Array.from(document.querySelectorAll('.item-checkbox:checked'))
        .map(checkbox => checkbox.dataset.itemId);
}

function checkoutSelectedItems(itemIds) {
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
    window.location.href = '/checkout';
}

// ===== EVENT LISTENERS SETUP =====
function initializeCartSystem() {
    // Initialize price displays
    updateAllItemPrices();
    updateSelectedTotals();
    updateCheckoutButtons();

    // Quantity buttons - single event listener using delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quantity-btn')) {
            e.preventDefault();
            handleQuantityChange(e.target.closest('.quantity-btn'));
        }
    });

    // Quantity input changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const cartItem = e.target.closest('.cart-item');
            updateItemPriceDisplay(cartItem);
            updateSelectedTotals();
            // Note: We don't auto-update server on input change to avoid too many requests
        }
    });

    // Remove item buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.preventDefault();
            const itemId = e.target.closest('.remove-item').dataset.itemId;
            if (confirm('Are you sure you want to remove this item?')) {
                removeCartItem(itemId, e.target.closest('.remove-item'));
            }
        }
    });

    // Checkbox handlers
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-checkbox')) {
            updateStoreCheckbox(e.target.dataset.store);
            updateSelectAllCheckbox();
            updateSelectedTotals();
            updateDeleteButton();
            updateCheckoutButtons();
        }
    });

    // Store checkbox handlers
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('store-checkbox')) {
            const storeName = e.target.dataset.store;
            const isChecked = e.target.checked;
            
            document.querySelectorAll(`.item-checkbox[data-store="${storeName}"]`).forEach(itemCheckbox => {
                itemCheckbox.checked = isChecked;
            });
            
            updateSelectAllCheckbox();
            updateSelectedTotals();
            updateDeleteButton();
            updateCheckoutButtons();
        }
    });

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('select-all-items');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            document.querySelectorAll('.store-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateSelectedTotals();
            updateDeleteButton();
            updateCheckoutButtons();
        });
    }

    // Delete selected button
    const deleteSelectedBtn = document.getElementById('delete-selected');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedItems = getSelectedItems();
            
            if (selectedItems.length > 0) {
                if (confirm(`Are you sure you want to remove ${selectedItems.length} item(s) from your cart?`)) {
                    deleteSelectedItems(selectedItems);
                }
            }
        });
    }

    // Checkout buttons
    const checkoutSelectedBtn = document.getElementById('checkout-selected-btn');
    const checkoutAllBtn = document.getElementById('checkout-all-btn');

    if (checkoutSelectedBtn) {
        checkoutSelectedBtn.addEventListener('click', function() {
            const selectedItems = getSelectedItems();
            if (selectedItems.length > 0) {
                checkoutSelectedItems(selectedItems);
            }
        });
    }

    if (checkoutAllBtn) {
        checkoutAllBtn.addEventListener('click', function() {
            checkoutAllItems();
        });
    }
}

// ===== VOUCHER FUNCTIONS =====
function openVoucherDialog() {
    const dialog = document.getElementById('cartVoucherDialog');
    if (dialog) {
        dialog.classList.remove('hidden');
    }
}

function removeSelectedVoucher() {
    if (!confirm('Are you sure you want to remove this voucher?')) {
        return;
    }

    fetch('/cart/remove-voucher', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Voucher berhasil dihapus!', 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Gagal menghapus voucher', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing voucher:', error);
        showNotification('Gagal menghapus voucher', 'error');
    });
}

function applySelectedVoucher(voucher) {
    fetch('/cart/apply-voucher', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ voucher_id: voucher.id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Voucher berhasil diterapkan!', 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Gagal menerapkan voucher', 'error');
        }
    })
    .catch(error => {
        console.error('Error applying voucher:', error);
        showNotification('Gagal menerapkan voucher', 'error');
    });
}

// ===== GLOBAL WINDOW FUNCTIONS =====
// These functions need to be available globally for components to call
window.openVoucherDialog = openVoucherDialog;
window.removeSelectedVoucher = removeSelectedVoucher;
window.applySelectedVoucher = applySelectedVoucher;

// Voucher callback handlers for the dialog component
window.voucherSelectionHandler = function(voucher) {
    window.selectedVoucher = voucher;
    console.log('Voucher selected:', voucher);
};

window.applyVoucherHandler = function() {
    if (window.selectedVoucher) {
        applySelectedVoucher(window.selectedVoucher);
    }
};