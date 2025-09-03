{{-- resources/views/components/address-modal.blade.php --}}
@props([
    'modalId' => 'address-modal',
    'countries' => [
        'Indonesia' => 'Indonesia',
        'Malaysia' => 'Malaysia',
        'Singapore' => 'Singapore',
        'Thailand' => 'Thailand'
    ],
    'provinces' => [
        'Jawa Timur' => 'Jawa Timur',
        'Jawa Tengah' => 'Jawa Tengah',
        'Jawa Barat' => 'Jawa Barat',
        'DKI Jakarta' => 'DKI Jakarta',
        'Banten' => 'Banten',
        'Yogyakarta' => 'Yogyakarta',
        'Bali' => 'Bali',
        'Sumatra Utara' => 'Sumatra Utara',
        'Sumatra Barat' => 'Sumatra Barat',
        'Sumatra Selatan' => 'Sumatra Selatan',
        'Kalimantan Timur' => 'Kalimantan Timur',
        'Kalimantan Selatan' => 'Kalimantan Selatan',
        'Sulawesi Selatan' => 'Sulawesi Selatan',
        'Sulawesi Utara' => 'Sulawesi Utara'
    ],
    'addressLabels' => [
        'Home' => 'Home',
        'Office' => 'Office',
        'Other' => 'Other'
    ],
    'defaultCountry' => 'Indonesia',
    'showDefaultCheckbox' => true,
    'submitUrl' => null,
    'method' => 'POST'
])

<div id="{{ $modalId }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="{{ $modalId }}-title">Add New Address</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" onclick="closeAddressModal('{{ $modalId }}')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="{{ $modalId }}-form" class="mt-6" @if($submitUrl) action="{{ $submitUrl }}" method="{{ $method }}" @endif>
                <input type="hidden" id="{{ $modalId }}-address-id" name="address_id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @if($method !== 'POST')
                    @method($method)
                @endif

                <div class="space-y-4">
                    <!-- Address Label -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Label</label>
                        <select id="{{ $modalId }}-address-label" name="label" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                            @foreach($addressLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Recipient Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipient Name *</label>
                            <input type="text" id="{{ $modalId }}-recipient-name" name="recipient_name" required
                                   pattern="[a-zA-Z\s]+"
                                   placeholder="John Doe"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                            <input type="tel" id="{{ $modalId }}-recipient-phone" name="phone" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <!-- Address Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Address *</label>
                        <textarea id="{{ $modalId }}-street-address" name="street_address" rows="2" required
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                                  placeholder="Complete address including street name, number, and any additional details"></textarea>
                    </div>

                    <!-- Location Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Province *</label>
                            <select id="{{ $modalId }}-province" name="province" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                <option value="">Select Province</option>
                                @foreach($provinces as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                            <input type="text" id="{{ $modalId }}-city" name="city" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">District *</label>
                            <input type="text" id="{{ $modalId }}-district" name="district" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code *</label>
                            <input type="text" id="{{ $modalId }}-postal-code" name="postal_code" required
                                   pattern="[0-9]{5}"
                                   placeholder="12345"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                            <select id="{{ $modalId }}-country" name="country"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                @foreach($countries as $value => $label)
                                    <option value="{{ $value }}" @if($value === $defaultCountry) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Notes</label>
                            <input type="text" id="{{ $modalId }}-address-notes" name="address_notes"
                                   placeholder="Optional notes (e.g., building floor, landmark)"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <!-- Additional Options -->
                    @if($showDefaultCheckbox)
                    <div class="flex items-center space-x-4 pt-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="{{ $modalId }}-set-default" name="is_default" class="text-blue-600 mr-2 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Set as default address</span>
                        </label>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t dark:border-gray-700">
                    <button type="button" onclick="closeAddressModal('{{ $modalId }}')"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors duration-300">
                        <span id="{{ $modalId }}-save-btn-text">Save Address</span>
                        <svg id="{{ $modalId }}-save-btn-loading" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
