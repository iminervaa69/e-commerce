/**
 * Simplified Address Manager - Reduced ID dependencies
 */
class AddressManager {
    constructor() {
        this.instances = new Map();
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeSelectors();
            this.bindGlobalEvents();
        });
    }

    initializeSelectors() {
        const selectors = document.querySelectorAll('[x-data*="addressSelector"]');
        selectors.forEach(selector => {
            const selectorId = selector.id || `selector-${Math.random().toString(36).substr(2, 9)}`;
            selector.id = selectorId;
            this.createSelectorInstance(selectorId, selector);
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

            async init() {
                if (this.addresses.length === 0) {
                    await this.loadAddresses();
                } else {
                    this.setDefaultSelection();
                }
                this.listenForModalEvents();
            },

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

                    // Open modal with address data using event
                    window.openAddressModal(address);

                } catch (error) {
                    console.error('Error fetching address:', error);
                    this.errorMessage = 'Failed to load address details. Please try again.';
                } finally {
                    this.isEditingAddress = null;
                }
            },

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

                    this.addresses = this.addresses.filter(addr => addr.id !== addressId);

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

            clearMessages() {
                this.successMessage = '';
                this.errorMessage = '';
            },

            clearMessageAfterDelay() {
                setTimeout(() => {
                    this.clearMessages();
                }, 5000);
            },

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

    bindGlobalEvents() {
        // Simplified global function - no modal ID needed
        window.openAddressModal = (address = null) => {
            window.dispatchEvent(new CustomEvent('open-address-modal', { 
                detail: { address } 
            }));
        };
    }

    getInstance(selectorId) {
        return this.instances.get(selectorId);
    }

    getAllInstances() {
        return Array.from(this.instances.values());
    }
}

// Simplified Alpine.js function
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

    return manager ? manager.createSelectorInstance(selectorId, element) : {};
}

// Initialize the global manager
window.AddressManager = new AddressManager();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AddressManager;
}