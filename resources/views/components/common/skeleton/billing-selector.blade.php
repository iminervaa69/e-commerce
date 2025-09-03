{{-- resources/views/frontend/checkout/components/billing-selector.blade.php --}}
<div x-data="{
    billingMode: 'same',
    selectedBilling: null,
    billingAddresses: [],
    newBillingData: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        company: '',
        street_address: '',
        city: '',
        state: '',
        postal_code: '',
        country: 'Indonesia'
    }
}" x-init="loadBillingAddresses()" class="billing-selector">

    <!-- Billing Options -->
    <div class="space-y-4 mb-6">
        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300"
               :class="billingMode === 'saved' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
            <input type="radio" name="billing_option" value="saved" x-model="billingMode" class="mt-1 text-blue-600">
            <div class="ml-3">
                <div class="font-medium text-gray-900 dark:text-white">Select from saved addresses</div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Choose from your saved billing addresses</p>
            </div>
        </label>

        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300"
               :class="billingMode === 'new' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
            <input type="radio" name="billing_option" value="new" x-model="billingMode" class="mt-1 text-blue-600">
            <div class="ml-3">
                <div class="font-medium text-gray-900 dark:text-white">Enter new billing address</div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Add a new billing address</p>
            </div>
        </label>
    </div>

    <!-- Saved Addresses Selection -->
    <div x-show="billingMode === 'saved'" x-transition class="space-y-4">
        <div class="border-t dark:border-gray-700 pt-4">
            <h3 class="font-medium text-gray-900 dark:text-white mb-4">Select Billing Address</h3>

            <div x-show="billingAddresses.length === 0" class="flex items-center justify-center py-4">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Loading addresses...</span>
            </div>

            <div class="space-y-3">
                <template x-for="address in billingAddresses" :key="address.id">
                    <label class="block">
                        <div class="flex items-start p-3 border rounded-lg cursor-pointer transition-colors duration-300"
                             :class="selectedBilling === address.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400'">
                            <input type="radio" name="billing_address" :value="address.id" x-model="selectedBilling" class="mt-1 text-blue-600">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-900 dark:text-white" x-text="address.label || 'Address'"></h4>
                                    <span x-show="address.is_default" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Default
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    <p x-text="`${address.first_name} ${address.last_name}`" class="font-medium text-gray-900 dark:text-white"></p>
                                    <p x-text="address.company" x-show="address.company"></p>
                                    <p x-text="`${address.street_address}, ${address.city}`"></p>
                                    <p x-text="`${address.state} ${address.postal_code}, ${address.country}`"></p>
                                </div>
                            </div>
                        </div>
                    </label>
                </template>
            </div>
        </div>
    </div>

    <!-- New Address Form -->
    <div x-show="billingMode === 'new'" x-transition class="border-t dark:border-gray-700 pt-6">
        <h3 class="font-medium text-gray-900 dark:text-white mb-4">Billing Information</h3>
        <form id="new-billing-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                    <input type="text" x-model="newBillingData.first_name" name="billing_first_name"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                    <input type="text" x-model="newBillingData.last_name" name="billing_last_name"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                           required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" x-model="newBillingData.email" name="billing_email"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <input type="tel" x-model="newBillingData.phone" name="billing_phone"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                        required>
            </div>
        </form>
    </div>
</div>

<script>
function loadBillingAddresses() {
    setTimeout(() => {
        this.billingAddresses = [
            {
                id: 1,
                label: 'Home',
                first_name: 'M Rizal',
                last_name: 'Noerdin',
                email: 'rizal@example.com',
                phone: '+62 812-3456-7890',
                company: '',
                street_address: 'Jl. Poncosiwalan 160 Ngunut',
                city: 'Babadan',
                state: 'Jawa Timur',
                postal_code: '62856',
                country: 'Indonesia',
                is_default: true
            },
            {
                id: 2,
                label: 'Office',
                first_name: 'M Rizal',
                last_name: 'Noerdin',
                email: 'rizal.office@example.com',
                phone: '+62 812-3456-7890',
                company: 'PT. Example Corp',
                street_address: 'Jl. Sudirman No. 123',
                city: 'Surabaya',
                state: 'Jawa Timur',
                postal_code: '60271',
                country: 'Indonesia',
                is_default: false
            }
        ];
        const defaultBilling = this.billingAddresses.find(addr => addr.is_default);
        if (defaultBilling) {
            this.selectedBilling = defaultBilling.id;
        }
    }, 800);
}
</script>
