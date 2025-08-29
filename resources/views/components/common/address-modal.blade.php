{{-- resources/views/frontend/checkout/components/address-modal.blade.php --}}
<div id="address-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">Add New Address</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" onclick="closeAddressModal()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="address-modal-form" class="mt-6">
                <input type="hidden" id="address-id" name="address_id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="space-y-4">
                    <!-- Address Label -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address Label</label>
                        <select id="address-label" name="label" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                            <option value="Home">Home</option>
                            <option value="Office">Office</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Recipient Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipient Name *</label>
                            <input type="text" id="recipient-name" name="recipient_name" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                            <input type="tel" id="recipient-phone" name="phone" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <!-- Address Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Address *</label>
                        <textarea id="street-address" name="street_address" rows="2" required
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                                  placeholder="Complete address including street name, number, and any additional details"></textarea>
                    </div>

                    <!-- Location Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                            <input type="text" id="city" name="city" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State/Province *</label>
                            <select id="state" name="state" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                <option value="">Select State/Province</option>
                                <option value="Jawa Timur">Jawa Timur</option>
                                <option value="Jawa Tengah">Jawa Tengah</option>
                                <option value="Jawa Barat">Jawa Barat</option>
                                <option value="DKI Jakarta">DKI Jakarta</option>
                                <option value="Banten">Banten</option>
                                <option value="Yogyakarta">Yogyakarta</option>
                                <option value="Bali">Bali</option>
                                <option value="Sumatra Utara">Sumatra Utara</option>
                                <option value="Sumatra Barat">Sumatra Barat</option>
                                <option value="Sumatra Selatan">Sumatra Selatan</option>
                                <option value="Kalimantan Timur">Kalimantan Timur</option>
                                <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                                <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                                <option value="Sulawesi Utara">Sulawesi Utara</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code *</label>
                            <input type="text" id="postal-code" name="postal_code" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                            <select id="country" name="country"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                <option value="Indonesia">Indonesia</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Thailand">Thailand</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="flex items-center space-x-4 pt-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="set-default" name="is_default" class="text-blue-600 mr-2 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Set as default address</span>
                        </label>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t dark:border-gray-700">
                    <button type="button" onclick="closeAddressModal()"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors duration-300">
                        <span id="save-btn-text">Save Address</span>
                        <svg id="save-btn-loading" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddressModal(address = null) {
    const modal = document.getElementById('address-modal');
    const form = document.getElementById('address-modal-form');
    const title = document.getElementById('modal-title');
    const saveBtn = document.getElementById('save-btn-text');

    // Reset form
    form.reset();

    if (address) {
        // Edit mode
        title.textContent = 'Edit Address';
        saveBtn.textContent = 'Update Address';

        // Populate form with address data
        document.getElementById('address-id').value = address.id;
        document.getElementById('address-label').value = address.label || 'Home';
        document.getElementById('recipient-name').value = address.recipient_name;
        document.getElementById('recipient-phone').value = address.phone;
        document.getElementById('street-address').value = address.street_address;
        document.getElementById('city').value = address.city;
        document.getElementById('state').value = address.state;
        document.getElementById('postal-code').value = address.postal_code;
        document.getElementById('country').value = address.country;
        document.getElementById('set-default').checked = address.is_default;
    } else {
        // Add mode
        title.textContent = 'Add New Address';
        saveBtn.textContent = 'Save Address';
        document.getElementById('country').value = 'Indonesia'; // Default country
    }

    modal.classList.remove('hidden');
}

function closeAddressModal() {
    const modal = document.getElementById('address-modal');
    modal.classList.add('hidden');
}

// Handle form submission
document.getElementById('address-modal-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('save-btn-text');
    const loading = document.getElementById('save-btn-loading');

    // Show loading state
    saveBtn.classList.add('hidden');
    loading.classList.remove('hidden');

    // Simulate API call
    setTimeout(() => {
        // Hide loading state
        saveBtn.classList.remove('hidden');
        loading.classList.add('hidden');

        // Close modal
        closeAddressModal();

        // Reload addresses (in real implementation, you'd refresh the address list)
        console.log('Address saved successfully');

        // Show success message
        alert('Address saved successfully!');
    }, 2000);
});

// Close modal when clicking outside
document.getElementById('address-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddressModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('address-modal');
        if (!modal.classList.contains('hidden')) {
            closeAddressModal();
        }
    }
});
</script>
