<div class="space-y-4" x-data="{ paymentMethod: 'card' }">
    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300"
           :class="paymentMethod === 'card' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
        <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="text-blue-600">
        <div class="ml-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                </svg>
                <span class="font-medium text-gray-900 dark:text-white">Credit/Debit Card</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Visa, Mastercard, etc.</p>
        </div>
    </label>

    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token">

    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300"
           :class="paymentMethod === 'ewallet' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'">
        <input type="radio" name="payment_method" value="ewallet" x-model="paymentMethod" class="text-blue-600">
        <div class="ml-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                <span class="font-medium text-gray-900 dark:text-white">E-Wallet</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">GCash, ShopeePay, GrabPay, etc.</p>
        </div>
    </label>

    <!-- Card Form -->
    <div x-show="paymentMethod === 'card'" x-transition class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <form id="card-form" class="space-y-4">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                    <input type="text" id="card-number" placeholder="1234 5678 9012 3456"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                    <input type="text" id="card-expiry" placeholder="MM/YY"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                    <input type="text" id="card-cvv" placeholder="123"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                <input type="text" id="card-name" placeholder="John Doe"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
            </div>
        </form>
    </div>

    <div x-show="paymentMethod === 'ewallet'" x-transition class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <div class="grid grid-cols-2 gap-4">
            <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="PH_GCASH">
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto mb-2 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">G</span>
                    </div>
                    <div class="text-blue-600 font-semibold">GCash</div>
                </div>
            </button>
            <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="PH_SHOPEEPAY">
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto mb-2 bg-orange-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">S</span>
                    </div>
                    <div class="text-orange-600 font-semibold">ShopeePay</div>
                </div>
            </button>
            <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="ID_OVO">
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto mb-2 bg-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">O</span>
                    </div>
                    <div class="text-purple-600 font-semibold">OVO</div>
                </div>
            </button>
            <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500 bg-white dark:bg-gray-800" data-channel="ID_DANA">
                <div class="text-center">
                    <div class="w-8 h-8 mx-auto mb-2 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">D</span>
                    </div>
                    <div class="text-blue-500 font-semibold">DANA</div>
                </div>
            </button>
        </div>
    </div>
</div>
