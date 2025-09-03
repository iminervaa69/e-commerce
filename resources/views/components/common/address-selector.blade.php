{{-- resources/views/components/address-selector.blade.php --}}
@props([
    'name' => 'shipping_address',
    'selectedId' => null,
    'addresses' => [],
    'showAddButton' => true,
    'loadingText' => 'Loading addresses...',
    'emptyTitle' => 'No addresses found',
    'emptyDescription' => 'Get started by adding your first shipping address.',
    'addButtonText' => 'Add Address',
    'confirmDeleteText' => 'Are you sure you want to delete this address?',
    'apiEndpoint' => '/addresses/api',
    'modalId' => 'address-modal'
])

<div x-data="addressSelector()"
     x-init="init()"
     class="address-selector"
     data-selected-id="{{ $selectedId }}"
     data-addresses="{{ json_encode($addresses) }}"
     data-api-endpoint="{{ $apiEndpoint }}"
     data-modal-id="{{ $modalId }}"
     data-name="{{ $name }}"
     data-confirm-delete-text="{{ $confirmDeleteText }}">

    <!-- Saved Addresses -->
    <div class="space-y-4">
        <!-- Loading state -->
        <div x-show="isLoading && addresses.length === 0" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">{{ $loadingText }}</span>
        </div>

        <!-- Address List -->
        <template x-for="address in addresses" :key="address.id">
            <label class="block">
                <div class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-colors duration-300"
                     :class="selectedAddress === address.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'">
                    <input type="radio"
                           name="{{ $name }}"
                           :value="address.id"
                           x-model="selectedAddress"
                           class="mt-1 text-blue-600">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-medium text-gray-900 dark:text-white" x-text="address.label || 'Home'"></h3>
                                <span x-show="address.is_default" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                    Default
                                </span>
                            </div>
                            <div class="flex items-center space-x-2" x-show="!isLoading">
                                <button type="button" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                        @click.stop="editAddress(address.id)"
                                        :disabled="isEditingAddress === address.id">
                                    <span x-show="isEditingAddress !== address.id">Edit</span>
                                    <span x-show="isEditingAddress === address.id">Loading...</span>
                                </button>
                                <button type="button" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm"
                                        @click.stop="deleteAddress(address.id)"
                                        :disabled="isDeletingAddress === address.id">
                                    <span x-show="isDeletingAddress !== address.id">Delete</span>
                                    <span x-show="isDeletingAddress === address.id">Deleting...</span>
                                </button>
                            </div>
                        </div>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <p x-text="address.recipient_name" class="font-medium text-gray-900 dark:text-white"></p>
                            <p x-text="address.phone" class="text-gray-600 dark:text-gray-400"></p>
                            <p class="mt-1">
                                <span x-text="address.street_address"></span><br>
                                <span x-text="`${address.district}, ${address.city}`"></span><br>
                                <span x-text="`${address.province} ${address.postal_code}`"></span><br>
                                <span x-text="address.country || 'Indonesia'"></span>
                            </p>
                            <p x-show="address.address_notes" x-text="address.address_notes" class="text-xs text-gray-500 dark:text-gray-500 mt-1 italic"></p>
                        </div>
                    </div>
                </div>
            </label>
        </template>

        <!-- No addresses state -->
        <div x-show="!isLoading && addresses.length === 0" class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $emptyTitle }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $emptyDescription }}</p>
            @if($showAddButton)
                <div class="mt-6">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            @click="openAddressModal()">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $addButtonText }}
                    </button>
                </div>
            @endif
        </div>

        <!-- Add New Address Button (when there are addresses) -->
        <div x-show="!isLoading && addresses.length > 0" class="mt-4">
            @if($showAddButton)
                <button type="button" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        @click="openAddressModal()">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $addButtonText }}
                </button>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="successMessage" x-transition class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
        <p x-text="successMessage"></p>
    </div>

    <div x-show="errorMessage" x-transition class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
        <p x-text="errorMessage"></p>
    </div>
</div>

{{-- JavaScript is now handled by address-manager.js --}}
@push('scripts')
<script src="{{ asset('js/address-manager.js') }}"></script>
@endpush
