// Checkout functionality with Xendit integration
class CheckoutManager {
    constructor() {
        this.isProcessing = false;
        this.selectedEwalletChannel = null;
        this.init();
    }

    init() {
        // Set up Xendit
        if (typeof Xendit !== 'undefined') {
            Xendit.setPublishableKey(window.xenditPublicKey);
        }

        this.bindEvents();
    }

    bindEvents() {
        // Checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', (e) => this.handleCheckout(e));
        }

        // E-wallet selection
        const ewalletBtns = document.querySelectorAll('.ewallet-btn');
        ewalletBtns.forEach(btn => {
            btn.addEventListener('click', (e) => this.selectEwallet(e));
        });

        // Form validation
        const cardForm = document.getElementById('card-form');
        if (cardForm) {
            cardForm.addEventListener('input', () => this.validateCardForm());
        }
    }

    selectEwallet(event) {
        // Remove previous selection
        document.querySelectorAll('.ewallet-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        });

        // Add selection to clicked button
        const button = event.currentTarget;
        button.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        
        this.selectedEwalletChannel = button.getAttribute('data-channel');
    }

    validateCardForm() {
        const cardNumber = document.getElementById('card-number').value;
        const cardExpiry = document.getElementById('card-expiry').value;
        const cardCvv = document.getElementById('card-cvv').value;
        const cardName = document.getElementById('card-name').value;

        const isValid = cardNumber && cardExpiry && cardCvv && cardName;
        
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.disabled = !isValid;
        }

        return isValid;
    }

    async handleCheckout(event) {
        event.preventDefault();

        if (this.isProcessing) return;

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (paymentMethod === 'card') {
            await this.processCardPayment();
        } else if (paymentMethod === 'ewallet') {
            await this.processEwalletPayment();
        }
    }

    async processCardPayment() {
        try {
            this.setLoading(true);

            // Get billing data
            const billingData = this.getBillingData();
            if (!billingData) {
                this.showError('Please fill in all billing information');
                return;
            }

            // Get card data
            const cardData = this.getCardData();
            if (!cardData) {
                this.showError('Please fill in all card information');
                return;
            }

            // Create card token
            const tokenData = await this.createCardToken(cardData);
            if (!tokenData.token_id) {
                this.showError('Failed to process card information');
                return;
            }

            // Process payment
            const paymentData = {
                ...billingData,
                amount: this.getOrderTotal(),
                token_id: tokenData.token_id,
                authentication_id: tokenData.authentication_id,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            const response = await fetch('/payment/card', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect_url;
            } else {
                this.showError(result.message);
            }

        } catch (error) {
            console.error('Payment error:', error);
            this.showError('Payment processing failed. Please try again.');
        } finally {
            this.setLoading(false);
        }
    }

    async processEwalletPayment() {
        try {
            this.setLoading(true);

            if (!this.selectedEwalletChannel) {
                this.showError('Please select an e-wallet option');
                return;
            }

            const billingData = this.getBillingData();
            if (!billingData) {
                this.showError('Please fill in all billing information');
                return;
            }

            const paymentData = {
                ...billingData,
                amount: this.getOrderTotal(),
                channel_code: this.selectedEwalletChannel,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            const response = await fetch('/payment/ewallet', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            });

            const result = await response.json();

            if (result.success && result.checkout_url) {
                window.location.href = result.checkout_url;
            } else {
                this.showError(result.message);
            }

        } catch (error) {
            console.error('E-wallet payment error:', error);
            this.showError('Payment processing failed. Please try again.');
        } finally {
            this.setLoading(false);
        }
    }

    createCardToken(cardData) {
        return new Promise((resolve, reject) => {
            if (typeof Xendit === 'undefined') {
                reject(new Error('Xendit not loaded'));
                return;
            }

            Xendit.card.createToken({
                card_number: cardData.cardNumber.replace(/\s/g, ''),
                card_exp_month: cardData.expMonth,
                card_exp_year: cardData.expYear,
                card_cvv: cardData.cvv
            }, (error, response) => {
                if (error) {
                    reject(error);
                } else {
                    resolve(response);
                }
            });
        });
    }

    getBillingData() {
        const firstName = document.querySelector('input[name="first_name"]').value;
        const lastName = document.querySelector('input[name="last_name"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const phone = document.querySelector('input[name="phone"]').value;

        if (!firstName || !lastName || !email || !phone) {
            return null;
        }

        return { first_name: firstName, last_name: lastName, email, phone };
    }

    getCardData() {
        const cardNumber = document.getElementById('card-number').value;
        const cardExpiry = document.getElementById('card-expiry').value;
        const cardCvv = document.getElementById('card-cvv').value;

        if (!cardNumber || !cardExpiry || !cardCvv) {
            return null;
        }

        const [expMonth, expYear] = cardExpiry.split('/');

        return {
            cardNumber,
            expMonth: expMonth?.trim(),
            expYear: expYear?.trim().length === 2 ? '20' + expYear.trim() : expYear?.trim(),
            cvv: cardCvv
        };
    }

    getOrderTotal() {
        const totalElement = document.getElementById('total');
        const totalText = totalElement.textContent.replace('$', '');
        return parseFloat(totalText);
    }

    setLoading(isLoading) {
        this.isProcessing = isLoading;
        const checkoutBtn = document.getElementById('checkout-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');

        if (isLoading) {
            checkoutBtn.disabled = true;
            btnText.textContent = 'Processing...';
            btnLoading.classList.remove('hidden');
        } else {
            checkoutBtn.disabled = false;
            btnText.textContent = 'Complete Order';
            btnLoading.classList.add('hidden');
        }
    }

    showError(message) {
        // You can replace this with a more sophisticated notification system
        alert(message);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new CheckoutManager();
});

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CheckoutManager;
}