{{-- resources/views/components/billing-selector.blade.php --}}
@props([
    'name' => 'billing_information',
    'selectedId' => null,
    'billingInformation' => [],
    'showAddButton' => true,
    'loadingText' => 'Loading billing information...',
    'emptyTitle' => 'No billing information found',
    'emptyDescription' => 'Get started by adding your first billing information.',
    'addButtonText' => 'Add Billing Information',
    'confirmDeleteText' => 'Are you sure you want to delete this billing information?',
    'apiEndpoint' => '/billing/api',
    'modalId' => 'billing-modal'
])

@php
    // Ensure billingInformation is properly formatted
    $formattedBillingInfo = collect($billingInformation)->map(function($billing) {
        if (is_array($billing)) {
            return $billing;
        }
        return [
            'id' => $billing->id,
            'first_name' => $billing->first_name,
            'last_name' => $billing->last_name,
            'full_name' => $billing->full_name ?? ($billing->first_name . ' ' . $billing->last_name),
            'email' => $billing->email,
            'phone' => $billing->phone,
            'is_default' => $billing->is_default ?? false
        ];
    })->toArray();
@endphp

<div x-data="billingSelector()"
     x-init="init()"
     class="billing-selector"
     data-selected-id="{{ $selectedId }}"
     data-billing-information="{{ json_encode($formattedBillingInfo) }}"
     data-api-endpoint="{{ $apiEndpoint }}"
     data-modal-id="{{ $modalId }}"
     data-name="{{ $name }}"
     data-confirm-delete-text="{{ $confirmDeleteText }}">

    <!-- Debug info (remove in production) -->
    <div x-show="false" x-text="'Billing count: ' + billingInformation.length"></div>

    <!-- Saved Billing Information -->
    <div class="space-y-4">
        <!-- Loading state -->
        <div x-show="isLoading" 
             x-transition
             class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">{{ $loadingText }}</span>
        </div>

        <!-- Billing Information List -->
        <template x-for="billing in billingInformation" :key="billing.id">
            <label class="block">
                <div class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-colors duration-300"
                     :class="selectedBilling == billing.id ? 
                        'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 
                        'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'">
                    
                    <input type="radio"
                           :name="name"
                           :value="billing.id"
                           x-model="selectedBilling"
                           class="mt-1 text-blue-600">
                    
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-medium text-gray-900 dark:text-white" 
                                    x-text="billing.full_name || (billing.first_name + ' ' + billing.last_name)"></h3>
                                <span x-show="billing.is_default" 
                                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                    Default
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-2" x-show="!isLoading">
                                <button type="button" 
                                        class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                        @click.stop="editBilling(billing.id)"
                                        :disabled="isEditingBilling === billing.id">
                                    <span x-show="isEditingBilling !== billing.id">Edit</span>
                                    <span x-show="isEditingBilling === billing.id">Loading...</span>
                                </button>
                                
                                <button type="button" 
                                        class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm"
                                        @click.stop="deleteBilling(billing.id)"
                                        :disabled="isDeletingBilling === billing.id">
                                    <span x-show="isDeletingBilling !== billing.id">Delete</span>
                                    <span x-show="isDeletingBilling === billing.id">Deleting...</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <p x-text="billing.email"></p>
                            <p x-text="billing.phone"></p>
                        </div>
                    </div>
                </div>
            </label>
        </template>

        <!-- No billing information state -->
        <div x-show="!isLoading && billingInformation.length === 0" 
             x-transition
             class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $emptyTitle }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyDescription }}</p>
            
            @if($showAddButton)
                <div class="mt-6">
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            @click="openBillingModal()">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $addButtonText }}
                    </button>
                </div>
            @endif
        </div>

        <!-- Add New Billing Information Button (when there are billing records) -->
        <div x-show="!isLoading && billingInformation.length > 0" class="mt-4">
            @if($showAddButton)
                <button type="button" 
                        class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        @click="openBillingModal()">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $addButtonText }}
                </button>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="successMessage" 
         x-transition 
         class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
        <p x-text="successMessage"></p>
    </div>

    <div x-show="errorMessage" 
         x-transition 
         class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
        <p x-text="errorMessage"></p>
    </div>
</div>

{{-- Include the billing manager script --}}
@pushOnce('scripts')
<script src="{{ asset('js/billing-manager.js') }}"></script>
@endpushOnce