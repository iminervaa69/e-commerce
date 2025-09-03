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

        // Real-time form validation
        this.setupRealTimeValidation();
    }

    setupRealTimeValidation() {
        // Phone number validation
        const phoneInput = document.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                this.validatePhoneNumber(e.target);
            });
        }

        // Name validation (letters only)
        const nameInputs = document.querySelectorAll('input[name="first_name"], input[name="last_name"]');
        nameInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.validateNameField(e.target);
            });
        });

        // Email validation
        const emailInput = document.querySelector('input[name="email"]');
        if (emailInput) {
            emailInput.addEventListener('blur', (e) => {
                this.validateEmailField(e.target);
            });
        }
    }

    validatePhoneNumber(input) {
        const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
        const value = input.value.trim();

        this.showFieldValidation(input, phoneRegex.test(value),
            'Please enter a valid Indonesian phone number (e.g., +62812345678 or 0812345678)');
    }

    validateNameField(input) {
        const nameRegex = /^[a-zA-Z\s]{2,50}$/;
        const value = input.value.trim();

        this.showFieldValidation(input, nameRegex.test(value),
            'Name must contain only letters and spaces (2-50 characters)');
    }

    validateEmailField(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const value = input.value.trim();

        this.showFieldValidation(input, emailRegex.test(value),
            'Please enter a valid email address');
    }

    showFieldValidation(input, isValid, errorMessage) {
        // Remove existing validation messages
        const existingError = input.parentNode.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }

        input.classList.remove('border-red-500', 'border-green-500');

        if (input.value.trim() === '') {
            return; // Don't show validation for empty fields
        }

        if (isValid) {
            input.classList.add('border-green-500');
        } else {
            input.classList.add('border-red-500');

            const errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error text-red-500 text-sm mt-1';
            errorDiv.textContent = errorMessage;
            input.parentNode.appendChild(errorDiv);
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
        const cardNumber = document.getElementById('card-number')?.value || '';
        const cardExpiry = document.getElementById('card-expiry')?.value || '';
        const cardCvv = document.getElementById('card-cvv')?.value || '';
        const cardName = document.getElementById('card-name')?.value || '';

        const isValid = cardNumber.length >= 13 && cardExpiry && cardCvv.length >= 3 && cardName.length >= 2;

        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.disabled = !isValid;
        }

        return isValid;
    }

    validateAllRequiredFields() {
        const requiredFields = [
            'first_name', 'last_name', 'email', 'phone'
        ];

        const errors = [];

        for (const fieldName of requiredFields) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (!field || !field.value.trim()) {
                errors.push(`${fieldName.replace('_', ' ')} is required`);
            }
        }

        // Check payment method specific validations
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;

        if (paymentMethod === 'card') {
            if (!this.validateCardForm()) {
                errors.push('Please complete all card information');
            }
        } else if (paymentMethod === 'ewallet') {
            if (!this.selectedEwalletChannel) {
                errors.push('Please select an e-wallet option');
            }
        } else {
            errors.push('Please select a payment method');
        }

        // Check addresses
        const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
        if (!shippingAddress) {
            errors.push('Please select a shipping address');
        }

        const sameAsShipping = document.getElementById('same-as-shipping')?.checked;
        if (!sameAsShipping) {
            const billingAddress = document.querySelector('input[name="billing_address"]:checked');
            if (!billingAddress) {
                errors.push('Please select a billing address');
            }
        }

        return errors;
    }

    async handleCheckout(event) {
        event.preventDefault();

        if (this.isProcessing) return;

        // Validate all fields first
        const validationErrors = this.validateAllRequiredFields();
        if (validationErrors.length > 0) {
            this.showError('Please fix the following errors:\n• ' + validationErrors.join('\n• '));
            return;
        }

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

            console.log('Starting card payment process...');

            // Step 1: Create card token (amount now calculated server-side)
            const tokenData = await this.createCardToken(cardData);

            if (!tokenData.id) {
                this.showError('Failed to process card information');
                return;
            }

            console.log('Token created:', tokenData.id);

            // Step 2: Handle token based on its status
            console.log('Token status:', tokenData.status);

            if (tokenData.status === 'VERIFIED') {
                // No 3DS required, process payment directly
                await this.submitPayment(tokenData.id, tokenData.id, billingData);
            } else if (tokenData.status === 'IN_REVIEW' && tokenData.payer_authentication_url) {
                // 3DS required, handle popup using the token data
                await this.handle3DSAuthentication(tokenData, billingData);
            } else {
                this.showError('Token creation failed: ' + tokenData.status);
            }

        } catch (error) {
            console.error('Payment error:', error);
            this.showError('Payment processing failed: ' + (error.message || 'Please try again.'));
        } finally {
            this.setLoading(false);
        }
    }

    // SIMPLIFIED: Handle 3DS using popup closure detection
    async handle3DSAuthentication(tokenData, billingData) {
        console.log('3DS authentication required');
        console.log('Authentication URL:', tokenData.payer_authentication_url);

        // Show user that 3DS is required
        this.showInfo('3D Secure authentication required. Please complete the authentication in the popup window.');

        // Open popup for 3DS authentication
        const popup = window.open(
            tokenData.payer_authentication_url,
            '3ds-auth',
            'width=500,height=600,scrollbars=yes,resizable=yes'
        );

        if (!popup) {
            this.showError('Please enable popups for 3D Secure authentication');
            return;
        }

        // Simple approach: Check if popup is closed and then try payment
        const checkClosed = setInterval(async () => {
            if (popup.closed) {
                clearInterval(checkClosed);
                console.log('3DS popup closed, attempting payment...');

                const authUrl = tokenData.payer_authentication_url;
                const authIdMatch = authUrl.match(/authentications\/([a-zA-Z0-9]+)/);
                const authenticationId = authIdMatch ? authIdMatch[1] : tokenData.id;

                console.log('Token ID:', tokenData.id);
                console.log('Authentication ID:', authenticationId);

                try {
                    // Process payment - if 3DS failed, the backend will handle the error
                    await this.submitPayment(tokenData.id, authenticationId, billingData);
                } catch (error) {
                    console.error('Payment error after 3DS:', error);
                    this.showError('Payment failed after authentication. Please try again.');
                } finally {
                    this.setLoading(false);
                }
            }
        }, 1000); // Check every second

        // Clear interval after 10 minutes to prevent infinite checking
        setTimeout(() => {
            clearInterval(checkClosed);
            if (!popup.closed) {
                popup.close();
                this.showError('3D Secure authentication timed out.');
                this.setLoading(false);
            }
        }, 600000); // 10 minutes timeout
    }

    // UPDATED: Submit payment to backend (removed amount parameter)
    async submitPayment(tokenId, authenticationId, billingData) {
        console.log('Submitting payment with:', { tokenId, authenticationId });

        // Get address information
        const shippingAddress = document.querySelector('input[name="shipping_address"]:checked')?.value;
        const billingAddress = document.querySelector('input[name="billing_address"]:checked')?.value;
        const sameAsShipping = document.getElementById('same-as-shipping')?.checked;

        const paymentData = {
            ...billingData,
            token_id: tokenId,
            authentication_id: authenticationId,
            shipping_address_id: shippingAddress,
            billing_address_id: sameAsShipping ? shippingAddress : billingAddress,
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
            console.log('Payment successful, redirecting...');
            this.showSuccess('Payment successful! Redirecting...');
            setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 1000);
        } else {
            console.error('Payment failed:', result);
            if (result.errors) {
                // Show validation errors
                const errorMessages = Object.values(result.errors).flat();
                this.showError('Validation errors:\n• ' + errorMessages.join('\n• '));
            } else {
                this.showError(result.message || 'Payment failed');
            }
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

            // Get address information
            const shippingAddress = document.querySelector('input[name="shipping_address"]:checked')?.value;
            const billingAddress = document.querySelector('input[name="billing_address"]:checked')?.value;
            const sameAsShipping = document.getElementById('same-as-shipping')?.checked;

            const paymentData = {
                ...billingData,
                channel_code: this.selectedEwalletChannel,
                shipping_address_id: shippingAddress,
                billing_address_id: sameAsShipping ? shippingAddress : billingAddress,
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
                this.showSuccess('Redirecting to payment gateway...');
                setTimeout(() => {
                    window.location.href = result.checkout_url;
                }, 1000);
            } else {
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat();
                    this.showError('Validation errors:\n• ' + errorMessages.join('\n• '));
                } else {
                    this.showError(result.message || 'E-wallet payment failed');
                }
            }

        } catch (error) {
            console.error('E-wallet payment error:', error);
            this.showError('Payment processing failed. Please try again.');
        } finally {
            this.setLoading(false);
        }
    }


    // UPDATED: Create card token without amount (amount calculated server-side)
    createCardToken(cardData) {
        return new Promise((resolve, reject) => {
            if (typeof Xendit === 'undefined') {
                reject(new Error('Xendit not loaded'));
                return;
            }

            const billingData = this.getBillingData();

            if (!billingData) {
                reject(new Error('Billing information is required for token creation'));
                return;
            }

            // Format phone number for Xendit
            let formattedPhone = billingData.phone.replace(/\D/g, '');

            if (!formattedPhone.startsWith('62')) {
                if (formattedPhone.startsWith('0')) {
                    formattedPhone = formattedPhone.substring(1);
                }
                formattedPhone = '62' + formattedPhone;
            }

            formattedPhone = '+' + formattedPhone;

            // Token creation without amount (Xendit will validate with backend)
            const tokenData = {
                amount: 1, // Minimal amount for token creation, actual amount handled server-side
                card_number: cardData.cardNumber.replace(/\s/g, ''),
                card_exp_month: cardData.expMonth,
                card_exp_year: cardData.expYear,
                card_cvv: cardData.cvv,
                card_holder_first_name: billingData.first_name,
                card_holder_last_name: billingData.last_name,
                card_holder_email: billingData.email,
                card_holder_phone_number: formattedPhone,
                is_multiple_use: false // Single use token
            };

            console.log('Creating card token with data:', tokenData);

            Xendit.card.createToken(tokenData, (error, response) => {
                if (error) {
                    console.error('Xendit token creation error:', error);
                    reject(error);
                } else {
                    console.log('Xendit token creation success:', response);
                    resolve(response);
                }
            });
        });
    }

    getBillingData() {
        const firstName = document.querySelector('input[name="first_name"]')?.value?.trim();
        const lastName = document.querySelector('input[name="last_name"]')?.value?.trim();
        const email = document.querySelector('input[name="email"]')?.value?.trim();
        const phone = document.querySelector('input[name="phone"]')?.value?.trim();

        if (!firstName || !lastName || !email || !phone) {
            return null;
        }

        return {
            first_name: firstName,
            last_name: lastName,
            email: email.toLowerCase(),
            phone
        };
    }

    getCardData() {
        const cardNumber = document.getElementById('card-number')?.value;
        const cardExpiry = document.getElementById('card-expiry')?.value;
        const cardCvv = document.getElementById('card-cvv')?.value;

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

        // Disable/enable all form inputs during processing
        const formInputs = document.querySelectorAll('input, button, select');
        formInputs.forEach(input => {
            if (input.id !== 'checkout-btn') {
                input.disabled = isLoading;
            }
        });
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showInfo(message) {
        this.showNotification(message, 'info');
    }

    showNotification(message, type = 'error') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.checkout-notification');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `checkout-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md`;

        switch (type) {
            case 'success':
                notification.className += ' bg-green-500 text-white';
                break;
            case 'info':
                notification.className += ' bg-blue-500 text-white';
                break;
            default:
                notification.className += ' bg-red-500 text-white';
        }

        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-1">
                    <p class="text-sm font-medium">${message.replace(/\n/g, '<br>')}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                        class="ml-2 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds for success/info, 10 seconds for errors
        const timeout = type === 'error' ? 10000 : 5000;
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, timeout);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.checkoutManager = new CheckoutManager();
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = CheckoutManager;
}
