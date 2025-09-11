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


    async submitPayment(tokenId, authenticationId, additionalData = {}) {
        console.log('=== PAYMENT SUBMISSION DEBUG ===');
        console.log('Token ID:', tokenId);
        console.log('Authentication ID:', authenticationId);
        console.log('Additional Data:', additionalData);

        // Collect all checkout data
        const checkoutData = this.collectAllCheckoutData();
        console.log('Raw checkout data:', checkoutData);

        // Validate critical data before sending
        if (!this.checkoutTotals.total || this.checkoutTotals.total <= 0) {
            console.error('❌ Invalid total amount:', this.checkoutTotals.total);
            this.showError('Invalid order total. Please refresh the page and try again.');
            return;
        }

        if (!checkoutData.billing_information_id) {
            console.error('❌ Missing billing information ID:', checkoutData.billing_information_id);
            this.showError('Please select billing information.');
            return;
        }

        if (!tokenId) {
            console.error('❌ Missing token ID');
            this.showError('Payment token missing. Please try again.');
            return;
        }

        // Prepare final payment data - controller will get customer info from billing_information_id
        const paymentData = {
            // Payment tokens
            token_id: tokenId,
            authentication_id: authenticationId,

            // Address IDs - controller will fetch the actual data
            address_id: checkoutData.address_id,
            billing_information_id: checkoutData.billing_information_id,

            // Payment details
            payment_method: checkoutData.payment_method,
            amount: this.checkoutTotals.total,

            // Optional fields
            voucher_code: checkoutData.voucher_code || null,

            // CSRF token
            _token: checkoutData._token,

            // Merge any additional data
            ...additionalData
        };

        console.log('=== PAYMENT DATA VALIDATION ===');
        console.log('Amount:', paymentData.amount, '(type:', typeof paymentData.amount, ')');
        console.log('Token ID:', paymentData.token_id);
        console.log('Shipping address ID:', paymentData.address_id);
        console.log('Billing info ID:', paymentData.billing_information_id);
        console.log('CSRF Token:', paymentData._token ? 'Present' : 'Missing');

        console.log('=== FINAL PAYMENT PAYLOAD ===');
        console.log(JSON.stringify(paymentData, null, 2));

        try {
            console.log('Sending request to /checkout/process-card...');

            const response = await fetch('/checkout/process-card', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': paymentData._token
                },
                body: JSON.stringify(paymentData)
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', Object.fromEntries(response.headers.entries()));

            let result;
            const contentType = response.headers.get('content-type');

            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                const text = await response.text();
                console.error('❌ Non-JSON response:', text);
                this.showError('Server returned invalid response. Please check server logs.');
                return;
            }

            console.log('=== BACKEND RESPONSE ===');
            console.log('Success:', result.success);
            console.log('Message:', result.message);
            console.log('Full response:', result);

            if (result.success) {
                console.log('✅ Payment successful, redirecting...');
                this.showSuccess('Payment successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = result.redirect_url || '/checkout/success';
                }, 1000);
            } else {
                console.error('❌ Payment failed:', result);

                if (result.errors) {
                    console.error('Validation errors:', result.errors);
                    const errorMessages = Object.values(result.errors).flat();
                    this.showError('Validation errors:\n• ' + errorMessages.join('\n• '));
                } else if (result.message) {
                    this.showError(result.message);
                } else {
                    this.showError('Payment failed. Please try again.');
                }

                // Log additional debug info if available
                if (result.debug) {
                    console.log('Debug info from backend:', result.debug);
                }
            }
        } catch (error) {
            console.error('❌ Payment request failed:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            this.showError('Network error. Please check your connection and try again.');
        }
    }


    validateAllRequiredFields() {
        console.log('Validating all required fields...');

        const errors = [];

        // Validate billing information selection
        const billingInfo = this.getBillingData();
        if (!billingInfo || !billingInfo.billing_information_id) {
            errors.push('Please select billing information');
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

    async loadCheckoutTotals() {
        try {
            console.log('Loading checkout totals...');

            // Try to get totals from DOM first (static)
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

            // Check if we have selected addresses to calculate dynamic totals
            const shippingAddress = document.querySelector('input[name="shipping_address"]:checked');
            const billingInfo = document.querySelector('input[name="billing_information"]:checked');
            const voucherCode = document.querySelector('input[name="voucher_code"]')?.value;

            // If addresses are selected, get dynamic calculation
            if (shippingAddress || voucherCode) {
                console.log('Calculating dynamic totals...');
                await this.calculateDynamicTotals(
                    shippingAddress?.value,
                    billingInfo?.value,
                    voucherCode
                );
            }

            this.checkoutTotals.lastUpdated = new Date().toISOString();
            console.log('Loaded checkout totals:', this.checkoutTotals);

        } catch (error) {
            console.error('Error loading checkout totals:', error);
        }
    }

    async calculateDynamicTotals(shippingAddressId, billingInformationId, voucherCode) {
        try {
            console.log('=== CALCULATING DYNAMIC TOTALS ===');

            const csrfToken = document.querySelector('#csrf-token')?.value ||
                            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const requestData = {
                address_id: shippingAddressId,
                billing_information_id: billingInformationId,
                voucher_code: voucherCode,
                amount: this.checkoutTotals.total || 0
            };

            console.log('Request URL: /cart/calculate-totals');
            console.log('Request data:', requestData);

            const response = await fetch('/cart/calculate-totals', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(requestData)
            });

            console.log('Response status:', response.status);
            const result = await response.json();
            console.log('Response data:', result);

            if (result.success) {
                this.checkoutTotals = {
                    ...this.checkoutTotals,
                    ...result.totals,
                    lastUpdated: new Date().toISOString()
                };
                this.updateTotalsDisplay(result.totals);
                console.log('✅ Dynamic totals updated successfully');
            } else {
                console.error('❌ Calculate totals failed:', result);
                this.showError('Failed to update totals: ' + result.message);
            }

        } catch (error) {
            console.error('❌ Calculate totals request error:', error);
            this.showError('Failed to calculate totals. Please refresh and try again.');
        }
    }

    updateTotalsDisplay(totals) {
        // Update data attributes and display text
        const subtotalEl = document.querySelector('[data-subtotal]');
        const shippingEl = document.querySelector('[data-shipping]');
        const taxEl = document.querySelector('[data-tax]');
        const discountEl = document.querySelector('[data-discount]');
        const totalEl = document.querySelector('[data-total]');

        if (subtotalEl) {
            subtotalEl.dataset.subtotal = totals.subtotal;
            subtotalEl.textContent = `Rp${totals.subtotal.toLocaleString('id-ID')}`;
        }

        if (shippingEl) {
            shippingEl.dataset.shipping = totals.shipping;
            shippingEl.textContent = `Rp${totals.shipping.toLocaleString('id-ID')}`;
        }

        if (taxEl) {
            taxEl.dataset.tax = totals.tax;
            taxEl.textContent = `Rp${totals.tax.toLocaleString('id-ID')}`;
        }

        if (totalEl) {
            totalEl.dataset.total = totals.total;
            totalEl.textContent = `Rp${totals.total.toLocaleString('id-ID')}`;
        }

        // Handle discount display (may not exist initially)
        if (totals.discount > 0) {
            if (discountEl) {
                discountEl.dataset.discount = totals.discount;
                discountEl.textContent = `-Rp${totals.discount.toLocaleString('id-ID')}`;
                discountEl.parentElement.style.display = 'flex';
            }
        } else {
            if (discountEl) {
                discountEl.parentElement.style.display = 'none';
            }
        }
    }


    getBillingData() {
        console.log('Getting billing information ID...');

        const selectedBilling = document.querySelector('input[name="billing_information"]:checked');

        if (selectedBilling && selectedBilling.value) {
            console.log('Found billing information ID:', selectedBilling.value);
            return {
                billing_information_id: selectedBilling.value
            };
        }

        console.log('No billing information selected');
        return null;
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
        const billingInfo = this.getBillingData();
        const paymentMethod = this.getPaymentMethod();

        const checkoutData = {
            // Address information - just IDs
            address_id: shippingAddress?.value || null,
            billing_information_id: billingInfo?.billing_information_id || null,

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
                console.log('3DS popup closed, waiting for authentication to complete...');

                const authUrl = tokenData.payer_authentication_url;
                const authIdMatch = authUrl.match(/authentications\/([a-zA-Z0-9]+)/);
                const authenticationId = authIdMatch ? authIdMatch[1] : tokenData.id;

                // SOLUTION 1: Add delay to allow authentication to complete
                setTimeout(async () => {
                    try {
                        await this.submitPayment(tokenData.id, authenticationId, billingData);
                    } catch (error) {
                        console.error('Payment error after 3DS:', error);
                        this.showError('Payment failed after authentication. Please try again.');
                    } finally {
                        this.setLoading(false);
                    }
                }, 2000); // Wait 2 seconds after popup closes
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

    getCustomerInfoForXendit(billingInfoId) {
        console.log('Getting customer info for Xendit token creation...');

        try {
            // Method 1: Try to get from data attributes on the selected billing option
            const selectedInput = document.querySelector(`input[name="billing_information"][value="${billingInfoId}"]`);
            if (selectedInput) {
                const container = selectedInput.closest('[data-billing-info]');
                if (container) {
                    const billingData = JSON.parse(container.dataset.billingInfo || '{}');
                    if (billingData.first_name && billingData.email) {
                        // Format phone number for Xendit
                        let formattedPhone = billingData.phone.replace(/\D/g, '');
                        if (!formattedPhone.startsWith('62')) {
                            if (formattedPhone.startsWith('0')) {
                                formattedPhone = formattedPhone.substring(1);
                            }
                            formattedPhone = '62' + formattedPhone;
                        }
                        formattedPhone = '+' + formattedPhone;

                        return {
                            first_name: billingData.first_name,
                            last_name: billingData.last_name,
                            email: billingData.email,
                            phone: formattedPhone
                        };
                    }
                }
            }

            // Method 2: Try Alpine.js data
            const billingSelector = document.querySelector('.billing-selector');
            if (billingSelector && typeof Alpine !== 'undefined') {
                const alpineData = Alpine.$data(billingSelector);
                if (alpineData && alpineData.billingInformation) {
                    const selectedBilling = alpineData.billingInformation.find(
                        billing => billing.id == billingInfoId
                    );

                    if (selectedBilling) {
                        let formattedPhone = selectedBilling.phone.replace(/\D/g, '');
                        if (!formattedPhone.startsWith('62')) {
                            if (formattedPhone.startsWith('0')) {
                                formattedPhone = formattedPhone.substring(1);
                            }
                            formattedPhone = '62' + formattedPhone;
                        }
                        formattedPhone = '+' + formattedPhone;

                        return {
                            first_name: selectedBilling.first_name,
                            last_name: selectedBilling.last_name,
                            email: selectedBilling.email,
                            phone: formattedPhone
                        };
                    }
                }
            }

            console.error('Unable to find customer info for billing ID:', billingInfoId);
            return null;

        } catch (error) {
            console.error('Error getting customer info for Xendit:', error);
            return null;
        }
    }

    async processEwalletPayment() {
        try {
            this.setLoading(true);

            // Collect all checkout data
            const checkoutData = this.collectAllCheckoutData();

            if (!checkoutData.address_id || !checkoutData.billing_information_id) {
                this.showError('Please select both shipping address and billing information');
                return;
            }

            if (!this.selectedEwalletChannel) {
                this.showError('Please select an e-wallet option');
                return;
            }

            // Prepare e-wallet payment data - simplified to just send IDs
            const paymentData = {
                channel_code: this.selectedEwalletChannel,
                address_id: checkoutData.address_id,
                billing_information_id: checkoutData.billing_information_id,
                payment_method: 'ewallet',
                voucher_code: checkoutData.voucher_code,
                amount: this.checkoutTotals.total,
                _token: checkoutData._token
            };

            console.log('E-wallet payment data being sent:', paymentData);

            const response = await fetch('/checkout/process-ewallet', {
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

    createCardToken(cardData, billingInfo) {
        return new Promise((resolve, reject) => {
            if (typeof Xendit === 'undefined') {
                reject(new Error('Xendit not loaded'));
                return;
            }

            // Get billing information to construct customer details for Xendit token
            const billingInfoId = billingInfo?.billing_information_id;
            if (!billingInfoId) {
                reject(new Error('Billing information not selected'));
                return;
            }

            // For Xendit token creation, we need the actual customer details
            // We'll need to get these from the DOM or make them available somehow
            // For now, let's get them from form fields or data attributes

            const customerInfo = this.getCustomerInfoForXendit(billingInfoId);
            if (!customerInfo) {
                reject(new Error('Unable to get customer information for payment processing'));
                return;
            }

            // THIS IS THE DATA SENT TO XENDIT FOR TOKEN CREATION
            const tokenData = {
                amount: Math.round(this.checkoutTotals.total), // Convert to integer
                card_number: cardData.cardNumber.replace(/\s/g, ''),
                card_exp_month: cardData.expMonth,
                card_exp_year: cardData.expYear,
                card_cvv: cardData.cvv,
                card_holder_first_name: customerInfo.first_name,
                card_holder_last_name: customerInfo.last_name,
                card_holder_email: customerInfo.email,
                card_holder_phone_number: customerInfo.phone,
                is_multiple_use: false,
                should_authenticate: true
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
        // getCustomerInfoForXendit()
        // getBillingData()
    }

    logAllCheckoutData() {
        const data = this.collectAllCheckoutData();
        // console.log('=== COMPLETE CHECKOUT DATA ===');
        // console.log('Shipping Address ID:', data.address_id);
        // console.log('Billing Information ID:', data.billing_information_id);
        // console.log('Customer Info:', data.customer_info);
        // console.log('Payment Method:', data.payment_method);
        // console.log('E-wallet Channel:', data.ewallet_channel);
        // console.log('Voucher Code:', data.voucher_code);
        // console.log('Card Data:', data.card_data);
        // console.log('Order Totals:', data.order_totals);
        // console.log('=== END CHECKOUT DATA ===');
    }

    updateCheckoutTotals(newTotals) {
        this.checkoutTotals = {
            ...this.checkoutTotals,
            ...newTotals,
            lastUpdated: new Date().toISOString()
        };
        console.log('Checkout totals updated:', this.checkoutTotals);
    }

    getFormattedAmount() {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(this.checkoutTotals.total);
    }

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

    async checkAuthenticationStatus(authenticationId) {
        try {
            // This would require a backend endpoint to check auth status with Xendit
            const response = await fetch('/xendit/check-authentication', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    authentication_id: authenticationId
                })
            });

            const result = await response.json();
            return result.status === 'VERIFIED';
        } catch (error) {
            console.error('Error checking authentication status:', error);
            return false;
        }
    }

    async handle3DSAuthenticationWithMessages(tokenData, billingData) {
        console.log('3DS authentication required with message listening');

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

        // Listen for messages from popup
        const messageHandler = async (event) => {
            // Make sure message is from popup
            if (event.source !== popup) return;

            if (event.data && event.data.type === '3ds_complete') {
                console.log('3DS authentication completed via message');
                window.removeEventListener('message', messageHandler);

                const authUrl = tokenData.payer_authentication_url;
                const authIdMatch = authUrl.match(/authentications\/([a-zA-Z0-9]+)/);
                const authenticationId = authIdMatch ? authIdMatch[1] : tokenData.id;

                // Wait a bit then submit payment
                setTimeout(async () => {
                    try {
                        await this.submitPayment(tokenData.id, authenticationId, billingData);
                    } catch (error) {
                        console.error('Payment error after 3DS:', error);
                        this.showError('Payment failed after authentication. Please try again.');
                    } finally {
                        this.setLoading(false);
                    }
                }, 1000);
            }
        };

        window.addEventListener('message', messageHandler);

        // Fallback: check if popup is closed (in case messages don't work)
        const checkClosed = setInterval(() => {
            if (popup.closed) {
                clearInterval(checkClosed);
                window.removeEventListener('message', messageHandler);

                // Only proceed if we haven't already handled via messages
                setTimeout(async () => {
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
                }, 3000); // Wait longer when using fallback
            }
        }, 1000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (!window.xenditPublicKey) {
        console.error('Xendit public key not found! Make sure to set window.xenditPublicKey');
    }

    window.checkoutManager = new CheckoutManager();

    window.debugCheckout = () => {
        console.log('=== CHECKOUT DEBUG ===');
        window.checkoutManager.logCheckoutTotals();
        window.checkoutManager.logAllCheckoutData();

        const xenditErrors = window.checkoutManager.validateXenditRequirements();
        if (xenditErrors.length > 0) {
            console.error('Xendit validation errors:', xenditErrors);
        } else {
            console.log('✅ Xendit requirements validated');
        }
        console.log('=== END DEBUG ===');
    };
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = CheckoutManager;
}
