/**
 * Address Manager - Unified handler for address selector and modal
 * Handles both address selection/listing and modal add/edit functionality
 */
class AddressManager {
    constructor() {
        this.instances = new Map();
        this.init();
    }

    init() {
        // Auto-initialize all address selectors on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeSelectors();
            this.initializeModals();
            this.bindGlobalEvents();
        });
    }

    initializeSelectors() {
        const selectors = document.querySelectorAll('[x-data*="addressSelector"]');
        selectors.forEach(selector => {
            const selectorId = selector.id || `selector-${Math.random().toString(36).substr(2, 9)}`;
            selector.id = selectorId;

            // Initialize Alpine.js data for this selector
            this.createSelectorInstance(selectorId, selector);
        });
    }

    initializeModals() {
        const modals = document.querySelectorAll('[id*="address-modal"]');
        modals.forEach(modal => {
            this.initializeModal(modal.id);
        });
    }

    createSelectorInstance(selectorId, element) {
        const config = this.extractSelectorConfig(element);

        const instance = {
            selectedAddress: config.selectedId,
            addresses: config.addresses || [],
            isLoading: !config.addresses || config.addresses.length === 0,
            isEditingAddress: null,
            isDeletingAddress: null,
            successMessage: '',
            errorMessage: '',
            apiEndpoint: config.apiEndpoint,
            modalId: config.modalId,
            name: config.name,

            // Initialize the selector
            async init() {
                if (this.addresses.length === 0) {
                    await this.loadAddresses();
                } else {
                    this.setDefaultSelection();
                }
                this.listenForModalEvents();
            },

            // Load addresses from API
            async loadAddresses() {
                this.isLoading = true;
                this.clearMessages();

                try {
                    const response = await fetch(this.apiEndpoint, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    this.addresses = data.addresses || data.data || data;
                    this.setDefaultSelection();

                } catch (error) {
                    console.error('Error loading addresses:', error);
                    this.errorMessage = 'Failed to load addresses. Please try again.';
                } finally {
                    this.isLoading = false;
                }
            },

            // Edit address
            async editAddress(addressId) {
                this.isEditingAddress = addressId;
                this.clearMessages();

                try {
                    const response = await fetch(`${this.apiEndpoint}/${addressId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    const address = data.address || data.data || data;

                    // Open modal with address data
                    window.AddressManager.openModal(this.modalId, address);

                } catch (error) {
                    console.error('Error fetching address:', error);
                    this.errorMessage = 'Failed to load address details. Please try again.';
                } finally {
                    this.isEditingAddress = null;
                }
            },

            // Delete address
            async deleteAddress(addressId) {
                const confirmText = element.dataset.confirmDeleteText || 'Are you sure you want to delete this address?';
                if (!confirm(confirmText)) {
                    return;
                }

                this.isDeletingAddress = addressId;
                this.clearMessages();

                try {
                    const response = await fetch(`${this.apiEndpoint}/${addressId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Remove from local array
                    this.addresses = this.addresses.filter(addr => addr.id !== addressId);

                    // Update selection if deleted address was selected
                    if (this.selectedAddress === addressId) {
                        this.setDefaultSelection();
                    }

                    this.successMessage = 'Address deleted successfully.';
                    this.clearMessageAfterDelay();

                } catch (error) {
                    console.error('Error deleting address:', error);
                    this.errorMessage = 'Failed to delete address. Please try again.';
                    this.clearMessageAfterDelay();
                } finally {
                    this.isDeletingAddress = null;
                }
            },

            // Set default selection
            setDefaultSelection() {
                if (!this.selectedAddress && this.addresses.length > 0) {
                    const defaultAddress = this.addresses.find(addr => addr.is_default);
                    if (defaultAddress) {
                        this.selectedAddress = defaultAddress.id;
                    } else {
                        this.selectedAddress = this.addresses[0].id;
                    }
                }
            },

            // Clear messages
            clearMessages() {
                this.successMessage = '';
                this.errorMessage = '';
            },

            // Clear message after delay
            clearMessageAfterDelay() {
                setTimeout(() => {
                    this.clearMessages();
                }, 5000);
            },

            // Listen for modal events
            listenForModalEvents() {
                document.addEventListener('address-saved', () => {
                    this.loadAddresses();
                    this.successMessage = 'Address saved successfully.';
                    this.clearMessageAfterDelay();
                });

                document.addEventListener('address-updated', () => {
                    this.loadAddresses();
                    this.successMessage = 'Address updated successfully.';
                    this.clearMessageAfterDelay();
                });
            }
        };

        this.instances.set(selectorId, instance);
        return instance;
    }

    extractSelectorConfig(element) {
        const dataset = element.dataset;
        return {
            selectedId: dataset.selectedId,
            addresses: JSON.parse(dataset.addresses || '[]'),
            apiEndpoint: dataset.apiEndpoint || '/addresses/api',
            modalId: dataset.modalId || 'address-modal',
            name: dataset.name || 'shipping_address'
        };
    }

    initializeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const form = modal.querySelector('form');
        if (!form) return;

        // Handle form submission
        form.addEventListener('submit', (e) => this.handleModalSubmit(e, modalId));

        // Handle modal close events
        this.bindModalCloseEvents(modalId);
    }

    async handleModalSubmit(e, modalId) {
        e.preventDefault();

        const form = e.target;
        const saveBtn = document.getElementById(`${modalId}-save-btn-text`);
        const loading = document.getElementById(`${modalId}-save-btn-loading`);
        const addressId = document.getElementById(`${modalId}-address-id`).value;
        const formData = new FormData(form);

        // Show loading state
        if (saveBtn) saveBtn.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');

        try {
            const isUpdate = addressId && addressId !== '';
            const url = isUpdate ?
                `/addresses/api/${addressId}` :
                '/addresses/api';
            const method = isUpdate ? 'PUT' : 'POST';

            const requestConfig = isUpdate ? {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(Object.fromEntries(formData))
            } : {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            };

            const response = await fetch(url, requestConfig);

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to save address');
            }

            const data = await response.json();

            // Close modal
            this.closeModal(modalId);

            // Dispatch events
            const eventName = isUpdate ? 'address-updated' : 'address-saved';
            document.dispatchEvent(new CustomEvent(eventName, {
                detail: { address: data.address || data.data || data }
            }));

        } catch (error) {
            console.error('Error saving address:', error);
            alert('Error saving address: ' + error.message);
        } finally {
            // Hide loading state
            if (saveBtn) saveBtn.classList.remove('hidden');
            if (loading) loading.classList.add('hidden');
        }
    }

    openModal(modalId = 'address-modal', address = null) {
        const modal = document.getElementById(modalId);
        const form = document.getElementById(`${modalId}-form`);
        const title = document.getElementById(`${modalId}-title`);
        const saveBtn = document.getElementById(`${modalId}-save-btn-text`);

        if (!modal) {
            console.error('Modal not found:', modalId);
            return;
        }

        // Reset form
        if (form) form.reset();

        if (address) {
            // Edit mode
            if (title) title.textContent = 'Edit Address';
            if (saveBtn) saveBtn.textContent = 'Update Address';

            // Populate form fields
            this.populateModalForm(modalId, address);
        } else {
            // Add mode
            if (title) title.textContent = 'Add New Address';
            if (saveBtn) saveBtn.textContent = 'Save Address';

            // Set default country
            const countrySelect = document.getElementById(`${modalId}-country`);
            if (countrySelect) countrySelect.value = 'Indonesia';
        }

        modal.classList.remove('hidden');
    }

    populateModalForm(modalId, address) {
        const fields = [
            'address-id', 'address-label', 'recipient-name', 'recipient-phone',
            'street-address', 'city', 'province', 'district', 'postal-code',
            'country', 'address-notes'
        ];

        fields.forEach(fieldName => {
            const element = document.getElementById(`${modalId}-${fieldName}`);
            if (element) {
                const addressKey = fieldName.replace('-', '_').replace('address_', '');
                if (address[addressKey] !== undefined) {
                    element.value = address[addressKey];
                }
            }
        });

        // Handle checkbox for default address
        const defaultCheckbox = document.getElementById(`${modalId}-set-default`);
        if (defaultCheckbox && address.is_default !== undefined) {
            defaultCheckbox.checked = address.is_default;
        }
    }

    closeModal(modalId = 'address-modal') {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    bindModalCloseEvents(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Close button
        const closeBtn = modal.querySelector('[onclick*="closeAddressModal"]');
        if (closeBtn) {
            closeBtn.onclick = () => this.closeModal(modalId);
        }

        // Cancel button
        const cancelBtn = modal.querySelector('button[type="button"]:not([onclick*="closeAddressModal"])');
        if (cancelBtn && cancelBtn.textContent.includes('Cancel')) {
            cancelBtn.onclick = () => this.closeModal(modalId);
        }

        // Click outside to close
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal(modalId);
            }
        });

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                this.closeModal(modalId);
            }
        });
    }

    bindGlobalEvents() {
        // Global function for opening address modal
        window.openAddressModal = (modalId = 'address-modal', address = null) => {
            this.openModal(modalId, address);
        };

        // Global function for closing address modal
        window.closeAddressModal = (modalId = 'address-modal') => {
            this.closeModal(modalId);
        };
    }

    // Get instance by selector ID
    getInstance(selectorId) {
        return this.instances.get(selectorId);
    }

    // Get all instances
    getAllInstances() {
        return Array.from(this.instances.values());
    }
}

// Global Alpine.js function for address selector
function addressSelector() {
    const element = document.currentScript?.parentElement ||
                   document.querySelector('[x-data*="addressSelector"]');

    if (!element) {
        console.error('AddressSelector: Could not find element');
        return {};
    }

    const selectorId = element.id;
    const manager = window.AddressManager;

    if (manager && manager.instances.has(selectorId)) {
        return manager.getInstance(selectorId);
    }

    // Fallback creation if manager isn't ready
    return manager ? manager.createSelectorInstance(selectorId, element) : {};
}

// Initialize the global manager
window.AddressManager = new AddressManager();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AddressManager;
}
