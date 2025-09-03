/**
 * Improved Billing Information Manager
 */
class BillingManager {
    constructor() {
        this.instances = new Map();
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeSelectors();
                this.bindGlobalEvents();
            });
        } else {
            this.initializeSelectors();
            this.bindGlobalEvents();
        }
    }

    initializeSelectors() {
        const selectors = document.querySelectorAll('[x-data*="billingSelector"]');
        selectors.forEach(selector => {
            const selectorId = selector.id || `selector-${Math.random().toString(36).substr(2, 9)}`;
            selector.id = selectorId;
            
            if (!this.instances.has(selectorId)) {
                this.createSelectorInstance(selectorId, selector);
            }
        });
    }

    createSelectorInstance(selectorId, element) {
        const config = this.extractSelectorConfig(element);

        const instance = {
            selectedBilling: config.selectedId || null,
            billingInformation: config.billingInformation || [],
            isLoading: false,
            isEditingBilling: null,
            isDeletingBilling: null,
            successMessage: '',
            errorMessage: '',
            apiEndpoint: config.apiEndpoint,
            modalId: config.modalId,
            name: config.name,
            element: element,

            async init() {
                console.log('Initializing billing selector:', selectorId);
                
                // Only load from API if no billing information was provided
                if (this.billingInformation.length === 0) {
                    await this.loadBillingInformation();
                } else {
                    this.setDefaultSelection();
                }
                
                this.listenForModalEvents();
                
                // Force Alpine to update
                if (window.Alpine && this.element._x_dataStack) {
                    this.element._x_dataStack[0] = this;
                }
            },

            async loadBillingInformation() {
                console.log('Loading billing information from:', this.apiEndpoint);
                this.isLoading = true;
                this.clearMessages();

                try {
                    const response = await fetch(this.apiEndpoint, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('Loaded billing data:', data);
                    
                    // Handle different response structures
                    if (data.success === false) {
                        throw new Error(data.message || 'Failed to load billing information');
                    }
                    
                    this.billingInformation = data.billing_info || data.billing_information || data.data || data;
                    this.setDefaultSelection();

                } catch (error) {
                    console.error('Error loading billing information:', error);
                    this.errorMessage = error.message || 'Failed to load billing information. Please try again.';
                } finally {
                    this.isLoading = false;
                }
            },

            async editBilling(billingId) {
                console.log('Editing billing:', billingId);
                this.isEditingBilling = billingId;
                this.clearMessages();

                try {
                    const response = await fetch(`${this.apiEndpoint.replace('/api', '')}/${billingId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    const billing = data.billing_info || data.billing_information || data.data || data;

                    // Open modal with billing data
                    this.openBillingModal(billing);

                } catch (error) {
                    console.error('Error fetching billing information:', error);
                    this.errorMessage = 'Failed to load billing information. Please try again.';
                } finally {
                    this.isEditingBilling = null;
                }
            },

            async deleteBilling(billingId) {
                const confirmText = this.element.dataset.confirmDeleteText || 'Are you sure you want to delete this billing information?';
                if (!confirm(confirmText)) {
                    return;
                }

                console.log('Deleting billing:', billingId);
                this.isDeletingBilling = billingId;
                this.clearMessages();

                try {
                    // Build the correct endpoint URL for delete route
                    const deleteEndpoint = this.apiEndpoint.replace('/api', `/${billingId}`);
                    const response = await fetch(deleteEndpoint, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to delete billing information');
                    }

                    // Remove from local array
                    this.billingInformation = this.billingInformation.filter(billing => billing.id !== billingId);

                    // Update selection if deleted item was selected
                    if (this.selectedBilling === billingId) {
                        this.setDefaultSelection();
                    }

                    this.successMessage = 'Billing information deleted successfully.';
                    this.clearMessageAfterDelay();

                } catch (error) {
                    console.error('Error deleting billing information:', error);
                    this.errorMessage = error.message || 'Failed to delete billing information. Please try again.';
                    this.clearMessageAfterDelay();
                } finally {
                    this.isDeletingBilling = null;
                }
            },

            setDefaultSelection() {
                if (!this.selectedBilling && this.billingInformation.length > 0) {
                    // Try to find default billing
                    const defaultBilling = this.billingInformation.find(billing => billing.is_default);
                    if (defaultBilling) {
                        this.selectedBilling = defaultBilling.id;
                    } else {
                        // Select first billing if no default
                        this.selectedBilling = this.billingInformation[0].id;
                    }
                }
            },

            openBillingModal(billing = null) {
                if (typeof window.openBillingModal === 'function') {
                    window.openBillingModal(billing);
                } else {
                    // Fallback - dispatch custom event
                    window.dispatchEvent(new CustomEvent('open-billing-modal', { 
                        detail: { billing } 
                    }));
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
                // Remove existing listeners to prevent duplicates
                document.removeEventListener('billing-saved', this.handleBillingSaved);
                document.removeEventListener('billing-updated', this.handleBillingUpdated);
                
                // Bind methods to preserve context
                this.handleBillingSaved = this.handleBillingSaved.bind(this);
                this.handleBillingUpdated = this.handleBillingUpdated.bind(this);
                
                document.addEventListener('billing-saved', this.handleBillingSaved);
                document.addEventListener('billing-updated', this.handleBillingUpdated);
            },

            handleBillingSaved() {
                this.loadBillingInformation();
                this.successMessage = 'Billing information saved successfully.';
                this.clearMessageAfterDelay();
            },

            handleBillingUpdated() {
                this.loadBillingInformation();
                this.successMessage = 'Billing information updated successfully.';
                this.clearMessageAfterDelay();
            }
        };

        // Initialize the instance
        this.instances.set(selectorId, instance);
        
        // Auto-initialize if Alpine is ready
        setTimeout(() => {
            instance.init();
        }, 100);
        
        return instance;
    }

    extractSelectorConfig(element) {
        const dataset = element.dataset;
        
        let billingInformation = [];
        try {
            billingInformation = JSON.parse(dataset.billingInformation || '[]');
        } catch (e) {
            console.error('Error parsing billing information:', e);
        }
        
        return {
            selectedId: dataset.selectedId || null,
            billingInformation: billingInformation,
            apiEndpoint: dataset.apiEndpoint || '/billing/api',
            modalId: dataset.modalId || 'billing-modal',
            name: dataset.name || 'billing_information'
        };
    }

    bindGlobalEvents() {
        // Global function for opening billing modal
        window.openBillingModal = (billing = null) => {
            window.dispatchEvent(new CustomEvent('open-billing-modal', { 
                detail: { billing } 
            }));
        };
        
        // Debug function
        window.debugBillingManager = () => {
            console.log('Billing Manager Instances:', this.instances);
            console.log('All instance data:', Array.from(this.instances.entries()));
        };
    }

    getInstance(selectorId) {
        return this.instances.get(selectorId);
    }

    getAllInstances() {
        return Array.from(this.instances.values());
    }
}

// Alpine.js function - improved
function billingSelector() {
    const element = document.currentScript?.parentElement || 
                   document.querySelector('[x-data*="billingSelector"]');

    if (!element) {
        console.error('BillingSelector: Could not find element');
        return {
            selectedBilling: null,
            billingInformation: [],
            isLoading: false,
            init() {}
        };
    }

    const selectorId = element.id || `selector-${Math.random().toString(36).substr(2, 9)}`;
    element.id = selectorId;
    
    const manager = window.BillingManager;

    if (manager) {
        if (manager.instances.has(selectorId)) {
            return manager.getInstance(selectorId);
        } else {
            return manager.createSelectorInstance(selectorId, element);
        }
    }

    // Fallback if manager isn't ready
    console.warn('BillingManager not ready, creating fallback');
    return {
        selectedBilling: null,
        billingInformation: [],
        isLoading: false,
        init() {
            console.log('Fallback billing selector initialized');
        }
    };
}

// Initialize the global manager
if (typeof window !== 'undefined') {
    window.BillingManager = new BillingManager();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BillingManager;
}