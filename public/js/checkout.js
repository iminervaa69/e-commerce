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

    // FIXED: Single processCardPayment method with correct token handling
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
            
            // Check for 'id' instead of 'token_id'
            if (!tokenData.id) {
                this.showError('Failed to process card information');
                return;
            }

            // Get amount as number
            const amount = this.getOrderTotal();
            if (amount <= 0) {
                this.showError('Invalid order amount');
                return;
            }

            console.log('Token data received:', tokenData);
            console.log('Token ID:', tokenData.id);
            console.log('Token status:', tokenData.status);

            // Check if 3DS authentication is required
            if (tokenData.status === 'IN_REVIEW' && tokenData.payer_authentication_url) {
                // Handle 3DS authentication
                await this.handle3DSAuthentication(tokenData, billingData, amount);
                return;
            }

            // Process payment - use tokenData.id as token_id
            const paymentData = {
                ...billingData,
                amount: Number(amount),
                token_id: tokenData.id,
                authentication_id: tokenData.id, // Use same ID for now
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            console.log('Payment data being sent:', paymentData);

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
                this.showError(result.message || 'Payment failed');
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

            // Get amount as number
            const amount = this.getOrderTotal();
            if (amount <= 0) {
                this.showError('Invalid order amount');
                return;
            }

            const paymentData = {
                ...billingData,
                amount: Number(amount),
                channel_code: this.selectedEwalletChannel,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            console.log('E-wallet payment data being sent:', paymentData);

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
                this.showError(result.message || 'E-wallet payment failed');
            }

        } catch (error) {
            console.error('E-wallet payment error:', error);
            this.showError('Payment processing failed. Please try again.');
        } finally {
            this.setLoading(false);
        }
    }

    // Enhanced 3DS authentication handler
    async handle3DSAuthentication(tokenData, billingData, amount) {
        console.log('3DS authentication required');
        console.log('Authentication URL:', tokenData.payer_authentication_url);
        
        // Show user that 3DS is required
        this.showError('3D Secure authentication required. Please complete the authentication in the popup window.');
        
        // Open popup for 3DS authentication
        const popup = window.open(
            tokenData.payer_authentication_url,
            '3ds-auth',
            'width=500,height=600,scrollbars=yes,resizable=yes'
        );
        
        // Listen for popup completion
        const checkClosed = setInterval(async () => {
            if (popup.closed) {
                clearInterval(checkClosed);
                console.log('3DS popup closed');
                
                // After 3DS completion, the token should be ready for payment
                // You might need to get the authentication status from Xendit
                try {
                    const paymentData = {
                        ...billingData,
                        amount: Number(amount),
                        token_id: tokenData.id,
                        authentication_id: tokenData.id,
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    };

                    console.log('Processing payment after 3DS authentication:', paymentData);

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
                        this.showError(result.message || 'Payment failed after authentication');
                    }
                } catch (error) {
                    console.error('Payment error after 3DS:', error);
                    this.showError('Payment processing failed after authentication. Please try again.');
                } finally {
                    this.setLoading(false);
                }
            }
        }, 1000);
    }

    createCardToken(cardData) {
        return new Promise((resolve, reject) => {
            if (typeof Xendit === 'undefined') {
                reject(new Error('Xendit not loaded'));
                return;
            }

            const amount = this.getOrderTotal();
            const billingData = this.getBillingData();
            
            if (!billingData) {
                reject(new Error('Billing information is required for token creation'));
                return;
            }

            // Format phone number for Xendit (must be in international format)
            let formattedPhone = billingData.phone.replace(/\D/g, ''); // Remove non-digits
            
            // Add Indonesia country code if phone doesn't start with country code
            if (!formattedPhone.startsWith('62')) {
                // Remove leading zero if present (Indonesian numbers often start with 08)
                if (formattedPhone.startsWith('0')) {
                    formattedPhone = formattedPhone.substring(1);
                }
                formattedPhone = '62' + formattedPhone; // Add Indonesia country code
            }
            
            formattedPhone = '+' + formattedPhone; // Add + prefix
            
            console.log('Original phone:', billingData.phone);
            console.log('Formatted phone:', formattedPhone);
            
            const tokenData = {
                card_number: cardData.cardNumber.replace(/\s/g, ''),
                card_exp_month: cardData.expMonth,
                card_exp_year: cardData.expYear,
                card_cvv: cardData.cvv,
                card_holder_first_name: billingData.first_name,
                card_holder_last_name: billingData.last_name,
                card_holder_email: billingData.email,
                card_holder_phone_number: formattedPhone,
                amount: Math.round(amount * 100), // Convert to cents for token creation
                should_authenticate: true, // Enable 3DS
                is_multiple_use: false // Single use token
            };

            console.log('Card token data being sent to Xendit:', tokenData);

            Xendit.card.createToken(tokenData, (error, response) => {
                if (error) {
                    console.error('Xendit token creation error:', error);
                    
                    if (error.error_code === 'VALIDATION_ERROR' && error.message.includes('Amount')) {
                        console.log('Retrying with amount in dollars instead of cents...');
                        
                        const retryTokenData = {
                            ...tokenData,
                            amount: amount // Use dollars instead of cents
                        };
                        
                        Xendit.card.createToken(retryTokenData, (retryError, retryResponse) => {
                            if (retryError) {
                                console.error('Xendit token creation retry failed:', retryError);
                                reject(retryError);
                            } else {
                                console.log('Xendit token creation retry success:', retryResponse);
                                resolve(retryResponse);
                            }
                        });
                    } else {
                        reject(error);
                    }
                } else {
                    console.log('Xendit token creation success:', response);
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
        if (!totalElement) {
            console.error('Total element not found');
            return 0;
        }
        
        let totalText = totalElement.textContent || totalElement.innerText || '';
        console.log('Original total text:', totalText);
        
        totalText = totalText.replace(/[$₱₽€£¥,\s]/g, '');
        console.log('Cleaned total text:', totalText);
        
        const amount = parseFloat(totalText);
        console.log('Parsed amount:', amount);
        
        if (isNaN(amount) || amount < 0) {
            console.error('Failed to parse amount from:', totalElement.textContent);
            return 0;
        }
        
        return Math.round(amount * 100) / 100;
    }

    setLoading(isLoading) {
        this.isProcessing = isLoading;
        const checkoutBtn = document.getElementById('checkout-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');

        if (checkoutBtn) {
            if (isLoading) {
                checkoutBtn.disabled = true;
                if (btnText) btnText.textContent = 'Processing...';
                if (btnLoading) btnLoading.classList.remove('hidden');
            } else {
                checkoutBtn.disabled = false;
                if (btnText) btnText.textContent = 'Complete Order';
                if (btnLoading) btnLoading.classList.add('hidden');
            }
        }
    }

    showError(message) {
        alert(message);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.checkoutManager = new CheckoutManager();
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = CheckoutManager;
}