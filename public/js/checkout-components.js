// checkout-components.js
document.addEventListener('DOMContentLoaded', function() {
    initializeCheckoutComponents();
});

function initializeCheckoutComponents() {
    initAddressSelector();

    initBillingSelector();

    initPaymentMethodHandlers();

    initCheckoutButton();
}

function initAddressSelector() {
    const addNewAddressBtn = document.getElementById('add-new-address');
    if (addNewAddressBtn) {
        addNewAddressBtn.addEventListener('click', function() {
            openAddressModal();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.name === 'shipping_address') {
            const selectedAddressId = e.target.value;
            document.getElementById('shipping-address-id').value = selectedAddressId;
            console.log('Selected shipping address:', selectedAddressId);

            updateShippingCosts(selectedAddressId);
        }
    });
}

function initBillingSelector() {
    const sameAsShippingCheckbox = document.getElementById('same-as-shipping');
    if (sameAsShippingCheckbox) {
        sameAsShippingCheckbox.addEventListener('change', function() {
            const billingSelector = document.querySelector('.billing-selector');
            if (billingSelector) {
                const alpineData = Alpine.$data(billingSelector);
                if (this.checked) {
                    alpineData.billingMode = 'same';
                }
            }
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.name === 'billing_option') {
            console.log('Billing option changed:', e.target.value);
        }

        if (e.target.name === 'billing_address') {
            const selectedBillingId = e.target.value;
            document.getElementById('billing-address-id').value = selectedBillingId;
            console.log('Selected billing address:', selectedBillingId);
        }
    });
}

function initPaymentMethodHandlers() {
    document.querySelectorAll('.ewallet-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.ewallet-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                btn.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            this.classList.remove('border-gray-300', 'dark:border-gray-600');
            this.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

            const channel = this.dataset.channel;
            document.getElementById('payment-channel').value = channel;
            console.log('Selected e-wallet:', channel);
        });
    });

    document.querySelectorAll('.bank-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.bank-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
                btn.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            this.classList.remove('border-gray-300', 'dark:border-gray-600');
            this.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

            const channel = this.dataset.channel;
            document.getElementById('payment-channel').value = channel;
            console.log('Selected bank:', channel);
        });
    });

    const cardForm = document.getElementById('card-form');
    if (cardForm) {
        const cardNumberInput = document.getElementById('card-number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
        }

        const cardExpiryInput = document.getElementById('card-expiry');
        if (cardExpiryInput) {
            cardExpiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }

        const cardCvvInput = document.getElementById('card-cvv');
        if (cardCvvInput) {
            cardCvvInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
            });
        }
    }
}

function initCheckoutButton() {
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            processCheckout();
        });
    }
}

function processCheckout() {
    const checkoutBtn = document.getElementById('checkout-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');

    // Validate checkout data
    if (!validateCheckoutData()) {
        return;
    }

    // Show loading state
    checkoutBtn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');

    // Collect checkout data
    const checkoutData = collectCheckoutData();
    console.log('Processing checkout with data:', checkoutData);

    // Process payment based on method
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;

    switch (paymentMethod) {
        case 'card':
            processCardPayment(checkoutData);
            break;
        case 'ewallet':
            processEWalletPayment(checkoutData);
            break;
        case 'bank_transfer':
            processBankTransferPayment(checkoutData);
            break;
        default:
            showError('Please select a payment method');
            resetCheckoutButton();
    }
}

function validateCheckoutData() {
    // Validate shipping address
    const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
    if (!shippingAddress) {
        showError('Please select a shipping address');
        return false;
    }

    // Validate billing information
    const billingOption = document.querySelector('input[name="billing_option"]:checked')?.value;
    if (!billingOption) {
        showError('Please select billing information');
        return false;
    }

    if (billingOption === 'saved') {
        const billingAddress = document.querySelector('input[name="billing_address"]:checked');
        if (!billingAddress) {
            showError('Please select a billing address');
            return false;
        }
    } else if (billingOption === 'new') {
        const requiredFields = ['billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone', 'billing_street_address', 'billing_city', 'billing_state', 'billing_postal_code'];
        for (const field of requiredFields) {
            const input = document.querySelector(`input[name="${field}"]`);
            if (!input || !input.value.trim()) {
                showError(`Please fill in all required billing fields`);
                return false;
            }
        }
    }

    // Validate payment method
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!paymentMethod) {
        showError('Please select a payment method');
        return false;
    }

    // Additional validation based on payment method
    if (paymentMethod.value === 'card') {
        const cardFields = ['card-number', 'card-expiry', 'card-cvv', 'card-name'];
        for (const field of cardFields) {
            const input = document.getElementById(field);
            if (!input || !input.value.trim()) {
                showError('Please fill in all card details');
                return false;
            }
        }
    } else if (paymentMethod.value === 'ewallet' || paymentMethod.value === 'bank_transfer') {
        const channel = document.getElementById('payment-channel').value;
        if (!channel) {
            showError(`Please select a ${paymentMethod.value === 'ewallet' ? 'e-wallet' : 'bank'}`);
            return false;
        }
    }

    return true;
}

function collectCheckoutData() {
    const data = {
        shipping_address_id: document.getElementById('shipping-address-id').value,
        billing_option: document.querySelector('input[name="billing_option"]:checked')?.value,
        payment_method: document.querySelector('input[name="payment_method"]:checked')?.value,
        amount: document.getElementById('total').textContent.replace('$', ''),
    };

    // Add billing data based on option
    if (data.billing_option === 'saved') {
        data.billing_address_id = document.getElementById('billing-address-id').value;
    } else if (data.billing_option === 'new') {
        data.billing_data = {
            first_name: document.querySelector('input[name="billing_first_name"]')?.value,
            last_name: document.querySelector('input[name="billing_last_name"]')?.value,
            email: document.querySelector('input[name="billing_email"]')?.value,
            phone: document.querySelector('input[name="billing_phone"]')?.value,
            company: document.querySelector('input[name="billing_company"]')?.value,
            street_address: document.querySelector('input[name="billing_street_address"]')?.value,
            city: document.querySelector('input[name="billing_city"]')?.value,
            state: document.querySelector('input[name="billing_state"]')?.value,
            postal_code: document.querySelector('input[name="billing_postal_code"]')?.value,
            country: 'Indonesia'
        };
    }

    // Add payment-specific data
    if (data.payment_method === 'card') {
        data.card_data = {
            number: document.getElementById('card-number').value,
            expiry: document.getElementById('card-expiry').value,
            cvv: document.getElementById('card-cvv').value,
            name: document.getElementById('card-name').value
        };
    } else if (data.payment_method === 'ewallet' || data.payment_method === 'bank_transfer') {
        data.channel_code = document.getElementById('payment-channel').value;
    }

    return data;
}

function processCardPayment(data) {
    // Implement card payment processing with Xendit
    console.log('Processing card payment...');

    // Simulate payment processing
    setTimeout(() => {
        // In real implementation, integrate with Xendit
        console.log('Card payment processed successfully');
        showSuccess('Payment processed successfully!');
        resetCheckoutButton();
    }, 3000);
}

function processEWalletPayment(data) {
    // Implement e-wallet payment processing
    console.log('Processing e-wallet payment...');

    // Simulate payment processing
    setTimeout(() => {
        console.log('E-wallet payment processed successfully');
        showSuccess('Payment processed successfully!');
        resetCheckoutButton();
    }, 2000);
}

function processBankTransferPayment(data) {
    // Implement bank transfer payment processing
    console.log('Processing bank transfer payment...');

    // Simulate payment processing
    setTimeout(() => {
        console.log('Bank transfer payment processed successfully');
        showSuccess('Payment processed successfully!');
        resetCheckoutButton();
    }, 2000);
}

function updateShippingCosts(addressId) {
    // Simulate shipping cost calculation based on address
    console.log('Updating shipping costs for address:', addressId);

    // In real implementation, call API to calculate shipping costs
    // For now, just simulate different costs
    const shippingCosts = {
        1: '$9.99',  // Home address
        2: '$12.99'  // Office address
    };

    const shippingElement = document.querySelector('.shipping-cost');
    if (shippingElement) {
        shippingElement.textContent = shippingCosts[addressId] || '$9.99';
    }

    // Recalculate total
    updateOrderTotal();
}

function updateOrderTotal() {
    // Simulate total calculation
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('$', ''));
    const shipping = parseFloat(document.querySelector('.shipping-cost')?.textContent.replace('$', '') || '9.99');
    const tax = parseFloat(document.querySelector('.tax-amount')?.textContent.replace('$', '') || '8.72');

    const total = subtotal + shipping + tax;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
    document.getElementById('payment-amount').value = total.toFixed(2);
}

function resetCheckoutButton() {
    const checkoutBtn = document.getElementById('checkout-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');

    checkoutBtn.disabled = false;
    btnText.classList.remove('hidden');
    btnLoading.classList.add('hidden');
}

function showError(message) {
    // Simple error display - replace with your preferred notification system
    alert('Error: ' + message);
}

function showSuccess(message) {
    // Simple success display - replace with your preferred notification system
    alert('Success: ' + message);
}

// Global functions for modal access
window.openAddressModal = function(address = null) {
    openAddressModal(address);
};

window.closeAddressModal = function() {
    closeAddressModal();
};
