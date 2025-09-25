// resources/js/checkout.js
class CheckoutManager {
    constructor() {
        this.isProcessing = false;
        this.selectedPaymentMethod = 'card';
        this.selectedEwalletChannel = null;
        this.xenditPublicKey = null;

        this.init();
    }

    init() {
        console.log('Initializing CheckoutManager...');

        // Get Xendit public key from window (set by Blade)
        this.xenditPublicKey = window.xenditPublicKey;

        if (!this.xenditPublicKey) {
            console.error('Xendit public key not found!');
            return;
        }

        // Initialize Xendit with public key
        if (typeof Xendit !== 'undefined') {
            Xendit.setPublishableKey(this.xenditPublicKey);
            console.log('Xendit initialized with public key');
        } else {
            console.error('Xendit SDK not loaded!');
            return;
        }

        this.setupEventListeners();
        this.initializePaymentMethods();
    }

    setupEventListeners() {
        // Payment method radio buttons
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.selectedPaymentMethod = e.target.value;
                console.log('Payment method changed:', this.selectedPaymentMethod);
            });
        });

        // E-wallet buttons
        document.querySelectorAll('.ewallet-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.selectEwalletChannel(e.currentTarget.dataset.channel);
            });
        });

        // Card input formatting
        this.setupCardInputFormatting();
    }

    initializePaymentMethods() {
        // Set default payment method
        const cardRadio = document.querySelector('input[name="payment_method"][value="card"]');
        if (cardRadio) {
            cardRadio.checked = true;
            this.selectedPaymentMethod = 'card';
        }
    }

    setupCardInputFormatting() {
        const cardNumberInput = document.getElementById('card-number');
        const cardExpiryInput = document.getElementById('card-expiry');
        const cardCvvInput = document.getElementById('card-cvv');

        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                if (formattedValue.length <= 19) {
                    e.target.value = formattedValue;
                }
            });
        }

        if (cardExpiryInput) {
            cardExpiryInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }

        if (cardCvvInput) {
            cardCvvInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.substring(0, 4);
            });
        }
    }

    selectEwalletChannel(channel) {
        this.selectedEwalletChannel = channel;

        // Update UI to show selected e-wallet
        document.querySelectorAll('.ewallet-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'bg-blue-50');
            btn.classList.add('border-gray-300');
        });

        const selectedBtn = document.querySelector(`[data-channel="${channel}"]`);
        if (selectedBtn) {
            selectedBtn.classList.remove('border-gray-300');
            selectedBtn.classList.add('border-blue-500', 'bg-blue-50');
        }

        console.log('Selected e-wallet channel:', channel);
    }

    // Main checkout handler called from order summary component
    async handleCheckout(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }

        if (this.isProcessing) {
            console.log('Payment already processing...');
            return;
        }

        // Validate required fields
        if (!this.validateCheckout()) {
            return;
        }

        this.isProcessing = true;
        this.updateUIProcessing(true);

        try {
            if (this.selectedPaymentMethod === 'card') {
                await this.processCardPayment();
            } else if (this.selectedPaymentMethod === 'ewallet') {
                await this.processEwalletPayment();
            } else {
                throw new Error('Invalid payment method selected');
            }
        } catch (error) {
            console.error('Checkout error:', error);
            this.showError('Payment failed: ' + error.message);
        } finally {
            this.isProcessing = false;
            this.updateUIProcessing(false);
        }
    }

    validateCheckout() {
        // Check shipping address
        const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
        if (!shippingAddress) {
            this.showError('Please select a shipping address');
            return false;
        }

        // Check billing information
        const billingInfo = document.querySelector('input[name="billing_information"]:checked');
        if (!billingInfo) {
            this.showError('Please select billing information');
            return false;
        }

        // Payment method specific validation
        if (this.selectedPaymentMethod === 'card') {
            return this.validateCardForm();
        } else if (this.selectedPaymentMethod === 'ewallet') {
            if (!this.selectedEwalletChannel) {
                this.showError('Please select an e-wallet option');
                return false;
            }
        }

        return true;
    }

    validateCardForm() {
        const cardNumber = document.getElementById('card-number')?.value?.replace(/\s/g, '');
        const cardExpiry = document.getElementById('card-expiry')?.value;
        const cardCvv = document.getElementById('card-cvv')?.value;
        const cardName = document.getElementById('card-name')?.value;

        if (!cardNumber || cardNumber.length < 13) {
            this.showError('Please enter a valid card number');
            return false;
        }

        if (!cardExpiry || !cardExpiry.match(/^\d{2}\/\d{2}$/)) {
            this.showError('Please enter a valid expiry date (MM/YY)');
            return false;
        }

        if (!cardCvv || cardCvv.length < 3) {
            this.showError('Please enter a valid CVV');
            return false;
        }

        if (!cardName || cardName.trim().length < 2) {
            this.showError('Please enter cardholder name');
            return false;
        }

        return true;
    }

    async processCardPayment() {
        console.log('Processing card payment...');

        // Get card details
        const cardData = this.getCardData();
        if (!cardData) {
            throw new Error('Invalid card data');
        }

        // Get order details
        const orderData = this.getOrderData();

        // Send to backend (no need for Xendit tokenization on frontend)
        const response = await fetch('/checkout/process-card-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ...cardData,
                ...orderData
            })
        });

        const result = await response.json();

        if (result.success) {
            if (result.requires_action && result.action_url) {
                // 3DS authentication required
                console.log('3DS authentication required, redirecting...');
                window.location.href = result.action_url;
            } else if (result.redirect_url) {
                // Payment successful
                console.log('Payment successful, redirecting...');
                window.location.href = result.redirect_url;
            } else if (result.poll_url) {
                // Poll for status
                console.log('Polling for payment status...');
                this.pollPaymentStatus(result.transaction_id);
            } else {
                throw new Error(result.message || 'Unknown response from server');
            }
        } else {
            throw new Error(result.message || 'Payment failed');
        }
    }

    async processEwalletPayment() {
        console.log('Processing e-wallet payment...');

        if (!this.selectedEwalletChannel) {
            throw new Error('No e-wallet channel selected');
        }

        const orderData = this.getOrderData();

        const response = await fetch('/checkout/process-ewallet-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                channel_code: this.selectedEwalletChannel,
                ...orderData
            })
        });

        const result = await response.json();

        if (result.success && result.checkout_url) {
            console.log('E-wallet payment initialized, redirecting...');
            window.location.href = result.checkout_url;
        } else {
            throw new Error(result.message || 'E-wallet payment failed');
        }
    }

    getCardData() {
        const cardNumber = document.getElementById('card-number')?.value?.replace(/\s/g, '');
        const cardExpiry = document.getElementById('card-expiry')?.value;
        const cardCvv = document.getElementById('card-cvv')?.value;
        const cardName = document.getElementById('card-name')?.value;

        if (!cardNumber || !cardExpiry || !cardCvv || !cardName) {
            return null;
        }

        // Parse expiry date
        const expiryParts = cardExpiry.split('/');
        const expiryMonth = parseInt(expiryParts[0]);
        const expiryYear = parseInt('20' + expiryParts[1]); // Assuming 2-digit year

        return {
            card_number: cardNumber,
            expiry_month: expiryMonth,
            expiry_year: expiryYear,
            cvv: cardCvv,
            cardholder_name: cardName.trim()
        };
    }

    getOrderData() {
        const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
        const billingInfo = document.querySelector('input[name="billing_information"]:checked');

        // Get total from order summary (you might need to adjust this selector)
        const totalElement = document.querySelector('[data-total-amount]');
        const totalAmount = totalElement ? totalElement.dataset.totalAmount : 0;

        return {
            address_id: shippingAddress?.value,
            billing_information_id: billingInfo?.value,
            amount: parseFloat(totalAmount)
        };
    }

    async pollPaymentStatus(transactionId) {
        const maxAttempts = 60; // 2 minutes (2 second intervals)
        let attempts = 0;

        const poll = async () => {
            try {
                const response = await fetch(`/checkout/payment-status/${transactionId}`);
                const result = await response.json();

                if (result.redirect_url) {
                    window.location.href = result.redirect_url;
                    return;
                }

                attempts++;
                if (attempts < maxAttempts && result.status === 'pending') {
                    setTimeout(poll, 2000); // Poll every 2 seconds
                } else {
                    // Timeout or final status
                    if (result.status === 'failed') {
                        this.showError('Payment failed. Please try again.');
                    } else {
                        this.showError('Payment status unclear. Please check your order history.');
                    }
                }
            } catch (error) {
                console.error('Error polling payment status:', error);
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(poll, 2000);
                }
            }
        };

        poll();
    }

    updateUIProcessing(isProcessing) {
        // Update order summary button (if accessible)
        const checkoutButton = document.querySelector('[data-checkout-button]');
        if (checkoutButton) {
            checkoutButton.disabled = isProcessing;
            checkoutButton.textContent = isProcessing ? 'Processing...' : 'Complete Order';
        }

        // Disable form inputs
        const formInputs = document.querySelectorAll('#card-form input, .ewallet-btn');
        formInputs.forEach(input => {
            input.disabled = isProcessing;
        });
    }

    showError(message) {
        // Create or update error message element
        let errorDiv = document.getElementById('checkout-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'checkout-error';
            errorDiv.className = 'bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4';

            // Insert at top of checkout area
            const checkoutContainer = document.querySelector('.lg\\:col-span-2');
            if (checkoutContainer) {
                checkoutContainer.insertBefore(errorDiv, checkoutContainer.firstChild);
            }
        }

        errorDiv.textContent = message;
        errorDiv.style.display = 'block';

        // Hide after 5 seconds
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);

        // Scroll to error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Debug method
    logAllCheckoutData() {
        console.log('=== CHECKOUT DEBUG DATA ===');
        console.log('Payment Method:', this.selectedPaymentMethod);
        console.log('E-wallet Channel:', this.selectedEwalletChannel);
        console.log('Selected Address:', document.querySelector('input[name="shipping_address"]:checked')?.value);
        console.log('Selected Billing:', document.querySelector('input[name="billing_information"]:checked')?.value);

        if (this.selectedPaymentMethod === 'card') {
            console.log('Card Data:', this.getCardData());
        }

        console.log('Order Data:', this.getOrderData());
        console.log('Is Processing:', this.isProcessing);
        console.log('=== END CHECKOUT DEBUG ===');
    }
}

// Initialize checkout manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.checkoutManager === 'undefined') {
        window.checkoutManager = new CheckoutManager();
        console.log('CheckoutManager initialized and available globally');
    }
});

// Make CheckoutManager available globally
window.CheckoutManager = CheckoutManager;
