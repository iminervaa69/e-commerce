{{-- resources/views/components/billing-modal.blade.php --}}
@props([
    'modalId' => 'billing-modal',
    'showDefaultCheckbox' => true,
    'submitUrl' => null,
    'method' => 'POST',
    'billing' => null
])

<div id="{{ $modalId }}" 
     x-data="billingModal({{ json_encode($billing) }})"
     x-show="isOpen" 
     x-transition
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
     style="display: none;"
     @keydown.escape="closeModal()">
    
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800"
         @click.away="closeModal()">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" @click="closeModal()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="submitForm()" class="mt-6">
                <input type="hidden" x-model="formData.id" name="billing_id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @if($method !== 'POST')
                    @method($method)
                @endif

                <div class="space-y-4">
                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                            <input type="text" x-model="formData.first_name" name="first_name" required
                                   pattern="[a-zA-Z\s]+"
                                   placeholder="John"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                            <input type="text" x-model="formData.last_name" name="last_name" required
                                   pattern="[a-zA-Z\s]+"
                                   placeholder="Doe"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                            <input type="email" x-model="formData.email" name="email" required
                                   placeholder="john.doe@example.com"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                            <input type="tel" x-model="formData.phone" name="phone" required
                                   placeholder="+62 812-3456-7890"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <!-- Additional Options -->
                    @if($showDefaultCheckbox)
                    <div class="flex items-center space-x-4 pt-4">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.is_default" name="is_default" class="text-blue-600 mr-2 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Set as default billing information</span>
                        </label>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t dark:border-gray-700">
                    <button type="button" @click="closeModal()"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors duration-300 disabled:opacity-50">
                        <template x-if="!isSubmitting">
                            <span x-text="submitButtonText"></span>
                        </template>
                        <template x-if="isSubmitting">
                            <div class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </div>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function billingModal(initialBilling = null) {
    return {
        isOpen: false,
        isSubmitting: false,
        formData: {
            id: '',
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            is_default: false
        },

        get modalTitle() {
            return this.formData.id ? 'Edit Billing Information' : 'Add New Billing Information';
        },

        get submitButtonText() {
            return this.formData.id ? 'Update Billing Information' : 'Save Billing Information';
        },

        init() {
            if (initialBilling) {
                this.loadBilling(initialBilling);
            }

            // Listen for global open events
            window.addEventListener('open-billing-modal', (e) => {
                this.openModal(e.detail?.billing);
            });
        },

        openModal(billing = null) {
            if (billing) {
                this.loadBilling(billing);
            } else {
                this.resetForm();
            }
            this.isOpen = true;
        },

        closeModal() {
            this.isOpen = false;
            this.resetForm();
        },

        loadBilling(billing) {
            this.formData = { ...this.formData, ...billing };
        },

        resetForm() {
            this.formData = {
                id: '',
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                is_default: false
            };
        },

        async submitForm() {
            this.isSubmitting = true;

            try {
                const isUpdate = this.formData.id && this.formData.id !== '';
                const url = isUpdate ? `/billing/api/${this.formData.id}` : '/billing/api';
                const method = isUpdate ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to save billing information');
                }

                const data = await response.json();

                // Close modal
                this.closeModal();

                // Dispatch events
                const eventName = isUpdate ? 'billing-updated' : 'billing-saved';
                document.dispatchEvent(new CustomEvent(eventName, {
                    detail: { billing: data.billing_information || data.data || data }
                }));

            } catch (error) {
                console.error('Error saving billing information:', error);
                alert('Error saving billing information: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}

// Global functions for opening modal
window.openBillingModal = (billing = null) => {
    window.dispatchEvent(new CustomEvent('open-billing-modal', { 
        detail: { billing } 
    }));
};
</script>