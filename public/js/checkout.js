// Complete Checkout functionality with Xendit integration
class CheckoutManager {
    constructor() {
        this.isProcessing = false;
        this.selectedEwalletChannel = null;
        this.checkoutTotals = {
            subtotal: 0,
            shipping: 0,
            tax: 0,
            discount: 0,
            total: 0,
            lastUpdated: null,
            selectedVoucher: null
        };
        this.init();
    }

    init() {
        // Set up Xendit
        if (typeof Xendit !== 'undefined') {
            Xendit.setPublishableKey(window.xenditPublicKey);
            console.log('Xendit initialized with public key');
        } else {
            console.error('Xendit not loaded! Make sure to include Xendit SDK.');
        }

        this.bindEvents();
        this.setupCardInputFormatting();
        this.loadCheckoutTotals(); // Load totals on init
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

    // Load checkout totals (this should be called when page loads or cart changes)
    loadCheckoutTotals() {
        try {
            // Try to get totals from various sources
            const totalsElement = document.querySelector('[data-checkout-totals]');
            if (totalsElement) {
                const totalsData = JSON.parse(totalsElement.dataset.checkoutTotals);
                this.checkoutTotals = { ...this.checkoutTotals, ...totalsData };
            }

            // Alternative: Get from individual elements
            const subtotalEl = document.querySelector('[data-subtotal]');
            const shippingEl = document.querySelector('[data-shipping]');
            const taxEl = document.querySelector('[data-tax]');
            const discountEl = document.querySelector('[data-discount]');
            const totalEl = document.querySelector('[data-total]');

            if (subtotalEl) this.checkoutTotals.subtotal = parseFloat(subtotalEl.dataset.subtotal) || 0;
            if (shippingEl) this.checkoutTotals.shipping = parseFloat(shippingEl.dataset.shipping) || 0;
            if (taxEl) this.checkoutTotals.tax = parseFloat(taxEl.dataset.tax) || 0;
            if (discountEl) this.checkoutTotals.discount = parseFloat(discountEl.dataset.discount) || 0;
            if (totalEl) this.checkoutTotals.total = parseFloat(totalEl.dataset.total) || 0;

            this.checkoutTotals.lastUpdated = new Date().toISOString();
            
            console.log('Loaded checkout totals:', this.checkoutTotals);
        } catch (error) {
            console.error('Error loading checkout totals:', error);
        }
    }

    getBillingData() {
        console.log('Getting billing data...');
        
        try {
            // Method 1: Try from form fields directly (most reliable)
            const firstName = document.querySelector('input[name="first_name"]')?.value?.trim();
            const lastName = document.querySelector('input[name="last_name"]')?.value?.trim();
            const email = document.querySelector('input[name="email"]')?.value?.trim();
            const phone = document.querySelector('input[name="phone"]')?.value?.trim();

            if (firstName && lastName && email && phone) {
                console.log('Found billing data from form fields');
                return {
                    first_name: firstName,
                    last_name: lastName,
                    email: email.toLowerCase(),
                    phone: phone
                };
            }

            // Method 2: Try Alpine.js data access
            const billingSelector = document.querySelector('.billing-selector');
            
            if (billingSelector && typeof Alpine !== 'undefined') {
                const alpineData = Alpine.$data(billingSelector);
                
                if (alpineData && alpineData.selectedBilling) {
                    const selectedBilling = alpineData.billingInformation.find(
                        billing => billing.id == alpineData.selectedBilling
                    );
                    
                    if (selectedBilling) {
                        console.log('Found billing via Alpine.js:', selectedBilling);
                        return {
                            first_name: selectedBilling.first_name,
                            last_name: selectedBilling.last_name,
                            email: selectedBilling.email,
                            phone: selectedBilling.phone
                        };
                    }
                }
            }
            
            // Method 3: Fallback to data attributes
            const selectedInput = document.querySelector('input[name="billing_information"]:checked');
            
            if (selectedInput && billingSelector) {
                const selectedId = selectedInput.value;
                const billingData = JSON.parse(billingSelector.dataset.billingInformation || '[]');
                const selectedBilling = billingData.find(billing => billing.id == selectedId);
                
                if (selectedBilling) {
                    console.log('Found billing via data attributes:', selectedBilling);
                    return {
                        first_name: selectedBilling.first_name,
                        last_name: selectedBilling.last_name,
                        email: selectedBilling.email,
                        phone: selectedBilling.phone
                    };
                }
            }
            
            console.log('No billing data found');
            return null;
            
        } catch (error) {
            console.error('Error getting billing data:', error);
            return null;
        }
    }

    getPaymentMethod() {
        const paymentMethodInput = document.querySelector('input[name="payment_method"]:checked');
        return paymentMethodInput?.value || null;
    }

    getCardData() {
        const cardNumber = document.getElementById('card-number')?.value?.trim();
        const cardExpiry = document.getElementById('card-expiry')?.value?.trim();
        const cardCvv = document.getElementById('card-cvv')?.value?.trim();

        if (!cardNumber || !cardExpiry || !cardCvv) {
            return null;
        }

        // Parse expiry date
        const expiryParts = cardExpiry.split('/');
        if (expiryParts.length !== 2) {
            return null;
        }

        const expMonth = expiryParts[0]?.trim();
        const expYear = expiryParts[1]?.trim();

        // Convert 2-digit year to 4-digit
        const fullYear = expYear?.length === 2 ? '20' + expYear : expYear;

        return {
            cardNumber: cardNumber.replace(/\s/g, ''), // Remove spaces
            expMonth,
            expYear: fullYear,
            cvv: cardCvv
        };
    }

    getSelectedEwalletChannel() {
        const selectedEwallet = document.querySelector('.ewallet-btn.border-blue-500');
        return selectedEwallet?.dataset?.channel || this.selectedEwalletChannel;
    }

    setupCardInputFormatting() {
        // Card number formatting
        const cardNumberInput = document.getElementById('card-number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                if (formattedValue !== e.target.value) {
                    e.target.value = formattedValue;
                }
            });
        }
        
        // Expiry date formatting
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
        
        // CVV number only
        const cardCvvInput = document.getElementById('card-cvv');
        if (cardCvvInput) {
            cardCvvInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 4);
            });
        }
    }

    collectAllCheckoutData() {
        console.log('Collecting all checkout data...');
        
        const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
        const billingInfo = document.querySelector('input[name="billing_information"]:checked');
        const billingData = this.getBillingData();
        const paymentMethod = this.getPaymentMethod();
        
        const checkoutData = {
            // Address information
            shipping_address_id: shippingAddress?.value || null,
            billing_information_id: billingInfo?.value || null,
            
            // Customer billing information
            customer_info: billingData,
            
            // Payment information
            payment_method: paymentMethod,
            
            // Order totals
            order_totals: this.checkoutTotals,
            
            // Additional data
            voucher_code: document.querySelector('input[name="voucher_code"]')?.value || null,
            
            // CSRF token
            _token: document.querySelector('#csrf-token')?.value || 
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
        
        // Add payment-specific data
        if (paymentMethod === 'card') {
            const cardData = this.getCardData();
            if (cardData) {
                checkoutData.card_data = cardData;
            }
        } else if (paymentMethod === 'ewallet') {
            checkoutData.ewallet_channel = this.getSelectedEwalletChannel();
        }
        
        console.log('Collected checkout data:', checkoutData);
        return checkoutData;
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
            btn.classList.add('border-gray-300', 'dark:border-gray-600');
        });

        // Add selection to clicked button
        const button = event.currentTarget;
        button.classList.remove('border-gray-300', 'dark:border-gray-600');
        button.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

        this.selectedEwalletChannel = button.getAttribute('data-channel');
        console.log('Selected e-wallet channel:', this.selectedEwalletChannel);
    }

    validateCardForm() {
        const cardData = this.getCardData();
        
        if (!cardData) {
            return false;
        }
        
        // Basic validation
        const isValid = 
            cardData.cardNumber.length >= 13 && 
            cardData.cardNumber.length <= 19 &&
            cardData.expMonth && 
            cardData.expYear && 
            cardData.cvv.length >= 3 && 
            cardData.cvv.length <= 4;

        return isValid;
    }

    validateAllRequiredFields() {
        console.log('Validating all required fields...');
        
        const errors = [];
        
        // Get billing data (which includes customer info)
        const billingData = this.getBillingData();
        if (!billingData) {
            errors.push('Please complete all billing information');
        }
        
        // Validate addresses
        const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
        if (!shippingAddress) {
            errors.push('Please select a shipping address');
        }
        
        // Validate payment method
        const paymentMethod = this.getPaymentMethod();
        if (!paymentMethod) {
            errors.push('Please select a payment method');
        } else {
            // Payment method specific validation
            if (paymentMethod === 'card') {
                if (!this.validateCardForm()) {
                    errors.push('Please complete all card information correctly');
                }
            } else if (paymentMethod === 'ewallet') {
                const selectedChannel = this.getSelectedEwalletChannel();
                if (!selectedChannel) {
                    errors.push('Please select an e-wallet option');
                }
            }
        }

        // Validate order totals
        if (!this.checkoutTotals.total || this.checkoutTotals.total <= 0) {
            errors.push('Invalid order total');
        }
        
        console.log('Validation errors:', errors);
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

        const paymentMethod = this.getPaymentMethod();

        if (paymentMethod === 'card') {
            await this.processCardPayment();
        } else if (paymentMethod === 'ewallet') {
            await this.processEwalletPayment();
        }
    }

    // MAIN CARD PAYMENT PROCESS - SENDS DATA TO XENDIT
    async processCardPayment() {
        try {
            this.setLoading(true);

            // Get billing data
            const billingData = this.getBillingData();
            if (!billingData) {
                this.showError('Please complete all billing information');
                return;
            }

            // Get card data
            const cardData = this.getCardData();
            if (!cardData) {
                this.showError('Please complete all card information');
                return;
            }

            console.log('Starting card payment process...');
            console.log('Order total:', this.checkoutTotals.total);

            // Step 1: Create card token with Xendit
            const tokenData = await this.createCardToken(cardData, billingData);

            if (!tokenData.id) {
                this.showError('Failed to process card information');
                return;
            }

            console.log('Xendit token created:', tokenData.id);
            console.log('Token status:', tokenData.status);

            // Step 2: Handle token based on its status
            if (tokenData.status === 'VERIFIED') {
                // No 3DS required, process payment directly
                await this.submitPayment(tokenData.id, tokenData.id, billingData);
            } else if (tokenData.status === 'IN_REVIEW' && tokenData.payer_authentication_url) {
                // 3DS required, handle popup using the token data
                await this.handle3DSAuthentication(tokenData, billingData);
            } else {
                this.showError('Card verification failed: ' + tokenData.status);
            }

        } catch (error) {
            console.error('Payment error:', error);
            this.showError('Payment processing failed: ' + (error.message || 'Please try again.'));
        } finally {
            this.setLoading(false);
        }
    }

    // CREATE XENDIT CARD TOKEN - THIS IS WHERE DATA GOES TO XENDIT
    createCardToken(cardData, billingData) {
        return new Promise((resolve, reject) => {
            if (typeof Xendit === 'undefined') {
                reject(new Error('Xendit not loaded'));
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

            // THIS IS THE DATA SENT TO XENDIT FOR TOKEN CREATION
            const tokenData = {
                amount: Math.round(this.checkoutTotals.total), // Convert to integer (cents)
                card_number: cardData.cardNumber.replace(/\s/g, ''),
                card_exp_month: cardData.expMonth,
                card_exp_year: cardData.expYear,
                card_cvv: cardData.cvv,
                card_holder_first_name: billingData.first_name,
                card_holder_last_name: billingData.last_name,
                card_holder_email: billingData.email,
                card_holder_phone_number: formattedPhone,
                is_multiple_use: false, // Single use token
                should_authenticate: true // Enable 3DS if required
            };

            console.log('Creating Xendit token with data:', tokenData);

            // SEND DATA TO XENDIT
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

    // Handle 3DS authentication
    async handle3DSAuthentication(tokenData, billingData) {
        console.log('3DS authentication required');
        console.log('Authentication URL:', tokenData.payer_authentication_url);

        this.showInfo('3D Secure authentication required. Please complete the authentication in the popup window.');

        const popup = window.open(
            tokenData.payer_authentication_url,
            '3ds-auth',
            'width=500,height=600,scrollbars=yes,resizable=yes'
        );

        if (!popup) {
            this.showError('Please enable popups for 3D Secure authentication');
            return;
        }

        // Check if popup is closed and then process payment
        const checkClosed = setInterval(async () => {
            if (popup.closed) {
                clearInterval(checkClosed);
                console.log('3DS popup closed, attempting payment...');

                const authUrl = tokenData.payer_authentication_url;
                const authIdMatch = authUrl.match(/authentications\/([a-zA-Z0-9]+)/);
                const authenticationId = authIdMatch ? authIdMatch[1] : tokenData.id;

                try {
                    await this.submitPayment(tokenData.id, authenticationId, billingData);
                } catch (error) {
                    console.error('Payment error after 3DS:', error);
                    this.showError('Payment failed after authentication. Please try again.');
                } finally {
                    this.setLoading(false);
                }
            }
        }, 1000);

        // Timeout after 10 minutes
        setTimeout(() => {
            clearInterval(checkClosed);
            if (!popup.closed) {
                popup.close();
                this.showError('3D Secure authentication timed out.');
                this.setLoading(false);
            }
        }, 600000);
    }

    // Submit payment to your backend
    async submitPayment(tokenId, authenticationId, additionalData = {}) {
        console.log('Submitting payment with token:', tokenId);
        
        // Collect all checkout data
        const checkoutData = this.collectAllCheckoutData();
        
        // Prepare final payment data for your backend
        const paymentData = {
            ...checkoutData.customer_info,
            token_id: tokenId,
            authentication_id: authenticationId,
            shipping_address_id: checkoutData.shipping_address_id,
            billing_information_id: checkoutData.billing_information_id,
            payment_method: checkoutData.payment_method,
            voucher_code: checkoutData.voucher_code,
            order_totals: checkoutData.order_totals,
            _token: checkoutData._token,
            ...additionalData
        };
        
        console.log('Final payment data being sent to backend:', paymentData);
        
        try {
            const response = await fetch('/payment/card', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': paymentData._token
                },
                body: JSON.stringify(paymentData)
            });

            const result = await response.json();
            
            console.log('Payment response:', result);

            if (result.success) {
                console.log('Payment successful, redirecting...');
                this.showSuccess('Payment successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = result.redirect_url || '/checkout/success';
                }, 1000);
            } else {
                console.error('Payment failed:', result);
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat();
                    this.showError('Validation errors:\n• ' + errorMessages.join('\n• '));
                } else {
                    this.showError(result.message || 'Payment failed. Please try again.');
                }
            }
        } catch (error) {
            console.error('Payment request failed:', error);
            this.showError('Network error. Please check your connection and try again.');
        }
    }

    // E-WALLET PAYMENT PROCESS billing
    async processEwalletPayment() {
        try {
            this.setLoading(true);

            // Collect all checkout data
            const checkoutData = this.collectAllCheckoutData();
            
            if (!checkoutData.shipping_address_id || !checkoutData.billing_information_id) {
                this.showError('Please select both shipping and billing information');
                return;
            }

            if (!this.selectedEwalletChannel) {
                this.showError('Please select an e-wallet option');
                return;
            }

            // Prepare e-wallet payment data
            const paymentData = {
                ...checkoutData.customer_info,
                channel_code: this.selectedEwalletChannel,
                shipping_address_id: checkoutData.shipping_address_id,
                billing_information_id: checkoutData.billing_information_id,
                payment_method: 'ewallet',
                voucher_code: checkoutData.voucher_code,
                order_totals: checkoutData.order_totals,
                amount: this.checkoutTotals.total, // Include amount for e-wallet
                _token: checkoutData._token
            };

            console.log('E-wallet payment data being sent:', paymentData);

            const response = await fetch('/payment/ewallet', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': paymentData._token
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

        // Auto-remove after timeout
        const timeout = type === 'error' ? 10000 : 5000;
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, timeout);
    }

    // DEBUG METHODS
    logCheckoutTotals() {
        // console.log('=== CHECKOUT TOTALS ===');
        // console.log('Subtotal:', this.checkoutTotals.subtotal);
        // console.log('Shipping:', this.checkoutTotals.shipping);
        // console.log('Tax:', this.checkoutTotals.tax);
        // console.log('Discount:', this.checkoutTotals.discount);
        // console.log('Total:', this.checkoutTotals.total);
        // console.log('Last Updated:', this.checkoutTotals.lastUpdated);
        // console.log('Selected Voucher:', this.checkoutTotals.selectedVoucher);
        // console.log('=== END TOTALS ===');
    }

    logAllCheckoutData() {
        const data = this.collectAllCheckoutData();
        // console.log('=== COMPLETE CHECKOUT DATA ===');
        // console.log('Shipping Address ID:', data.shipping_address_id);
        // console.log('Billing Information ID:', data.billing_information_id);
        // console.log('Customer Info:', data.customer_info);
        // console.log('Payment Method:', data.payment_method);
        // console.log('E-wallet Channel:', data.ewallet_channel);
        // console.log('Voucher Code:', data.voucher_code);
        // console.log('Card Data:', data.card_data);
        // console.log('Order Totals:', data.order_totals);
        // console.log('=== END CHECKOUT DATA ===');
    }

    // Update checkout totals (call this when cart changes)
    updateCheckoutTotals(newTotals) {
        this.checkoutTotals = { 
            ...this.checkoutTotals, 
            ...newTotals, 
            lastUpdated: new Date().toISOString() 
        };
        console.log('Checkout totals updated:', this.checkoutTotals);
    }

    // Get formatted amount for display
    getFormattedAmount() {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(this.checkoutTotals.total);
    }

    // Validate Xendit requirements
    validateXenditRequirements() {
        const errors = [];

        if (typeof Xendit === 'undefined') {
            errors.push('Xendit SDK not loaded');
        }

        if (!window.xenditPublicKey) {
            errors.push('Xendit public key not set');
        }

        if (!this.checkoutTotals.total || this.checkoutTotals.total <= 0) {
            errors.push('Invalid order total');
        }

        return errors;
    }
}

// Initialize checkout manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Make sure Xendit public key is available
    if (!window.xenditPublicKey) {
        console.error('Xendit public key not found! Make sure to set window.xenditPublicKey');
    }

    // Initialize checkout manager
    window.checkoutManager = new CheckoutManager();
    
    // Make debug functions globally available
    window.debugCheckout = () => {
        console.log('=== CHECKOUT DEBUG ===');
        window.checkoutManager.logCheckoutTotals();
        window.checkoutManager.logAllCheckoutData();
        
        // Validate Xendit requirements
        const xenditErrors = window.checkoutManager.validateXenditRequirements();
        if (xenditErrors.length > 0) {
            console.error('Xendit validation errors:', xenditErrors);
        } else {
            console.log('✅ Xendit requirements validated');
        }
        console.log('=== END DEBUG ===');
    };
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CheckoutManager;
}