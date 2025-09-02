@extends('frontend.layouts.main')

@section('title')
Checkout
@endsection

@push('styles')
<script src="https://js.xendit.co/v1/xendit.min.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 pb-8" x-data="checkoutData()">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Checkout</h1>
            <p class="text-gray-600 dark:text-gray-400">Complete your order</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side - Checkout Form -->
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <!-- Shipping Address Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Alamat Pengiriman</h2>
                        </div>
                        <div class="p-6">
                            <!-- Selected Address Display -->
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-4 cursor-pointer hover:border-blue-500 transition-colors duration-300"
                                 @click="showAddressModal = true">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium text-green-600" x-text="selectedAddress.type"></span>
                                            <span class="text-xs text-gray-500 ml-2" x-show="selectedAddress.is_primary">• Utama</span>
                                        </div>
                                        <h3 class="font-medium text-gray-900 dark:text-white mb-1" x-text="selectedAddress.name"></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedAddress.phone"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="selectedAddress.full_address"></p>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-xs text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded mr-2">Sudah Pinpoint</span>
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Method</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4" x-data="{ paymentMethod: 'card' }">
                                <!-- Credit/Debit Card -->
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
                                
                                <!-- E-Wallet -->
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
                                <div x-show="paymentMethod === 'card'" x-transition class="mt-6">
                                    <form id="card-form" class="space-y-4">
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                                                <input type="text" id="card-number" placeholder="1234 5678 9012 3456" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                                                <input type="text" id="card-expiry" placeholder="MM/YY" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                                                <input type="text" id="card-cvv" placeholder="123" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                                            <input type="text" id="card-name" placeholder="John Doe" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                        </div>
                                    </form>
                                </div>

                                <!-- E-Wallet Selection -->
                                <div x-show="paymentMethod === 'ewallet'" x-transition class="mt-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500" data-channel="PH_GCASH">
                                            <div class="text-center">
                                                <div class="text-blue-600 font-semibold">GCash</div>
                                            </div>
                                        </button>
                                        <button type="button" class="ewallet-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 transition-colors duration-300 focus:border-blue-500" data-channel="PH_SHOPEEPAY">
                                            <div class="text-center">
                                                <div class="text-orange-600 font-semibold">ShopeePay</div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Billing Information</h2>
                        </div>
                        <div class="p-6">
                            <form id="billing-form" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                                        <input type="text" name="first_name" 
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                                        <input type="text" name="last_name" 
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <input type="email" name="email" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300 sticky top-4">
                    <div class="p-6 border-b dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Summary</h2>
                    </div>
                    <div class="p-6">
                        <!-- Order Items -->
                        <div class="space-y-4 mb-6">
                            <!-- Sample items - replace with your cart data -->
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 dark:text-white">Sample Product</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Qty: 1</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 dark:text-white">$99.00</p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="border-t dark:border-gray-700 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-white" id="subtotal">$99.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                <span class="text-gray-900 dark:text-white">$9.99</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-white">$8.72</span>
                            </div>
                            <div class="border-t dark:border-gray-700 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white" id="total">$117.71</span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <button id="checkout-btn" class="w-full mt-6 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="btn-text">Complete Order</span>
                            <svg id="btn-loading" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Selection Modal -->
    <div x-show="showAddressModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-50" @click="showAddressModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden" @click.stop>
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Daftar Alamat</h3>
                    <button @click="showAddressModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Tabs -->
                <div class="border-b dark:border-gray-700">
                    <div class="flex">
                        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="px-6 py-3 border-b-2 font-medium text-sm">
                            Semua Alamat
                        </button>
                        <button @click="activeTab = 'friends'" :class="activeTab === 'friends' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="px-6 py-3 border-b-2 font-medium text-sm">
                            Dari Teman
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="p-6 max-h-96 overflow-y-auto">
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                            <input type="text" placeholder="Tulis Nama Alamat / Kota / Kecamatan tujuan pengiriman" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Add New Address Button -->
                    <button @click="showAddAddressForm = true" class="w-full p-4 border-2 border-dashed border-green-300 dark:border-green-600 rounded-lg text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-300 mb-4">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Tambah Alamat Baru</span>
                        </div>
                    </button>

                    <!-- Address List -->
                    <div class="space-y-3" x-show="activeTab === 'all'">
                        <template x-for="address in addresses" :key="address.id">
                            <div class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors duration-300"
                                 :class="selectedAddress.id === address.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                                 @click="selectAddress(address)">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium text-green-600" x-text="address.type"></span>
                                            <span x-show="address.is_primary" class="text-xs text-gray-500 ml-2">• Utama</span>
                                        </div>
                                        <h3 class="font-medium text-gray-900 dark:text-white mb-1" x-text="address.name"></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="address.phone"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="address.full_address"></p>
                                    </div>
                                    <div class="flex items-center">
                                        <span x-show="address.is_pinpoint" class="text-xs text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded mr-2">Sudah Pinpoint</span>
                                        <svg x-show="selectedAddress.id === address.id" class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Action Buttons -->
                                <div class="flex items-center mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <button class="text-green-600 dark:text-green-400 text-sm font-medium hover:underline mr-4">Share</button>
                                    <button class="text-green-600 dark:text-green-400 text-sm font-medium hover:underline">Ubah Alamat</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Friends Addresses -->
                    <div class="space-y-3" x-show="activeTab === 'friends'">
                        <template x-for="address in friendsAddresses" :key="address.id">
                            <div class="border rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors duration-300"
                                 :class="selectedAddress.id === address.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                                 @click="selectAddress(address)">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium text-blue-600" x-text="address.type"></span>
                                        </div>
                                        <h3 class="font-medium text-gray-900 dark:text-white mb-1" x-text="address.name"></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="address.phone"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="address.full_address"></p>
                                    </div>
                                    <div class="flex items-center">
                                        <button class="text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-1 rounded text-sm font-medium mr-2">Pilih</button>
                                        <svg x-show="selectedAddress.id === address.id" class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State for Friends -->
                    <div x-show="activeTab === 'friends' && friendsAddresses.length === 0" class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada alamat dari teman</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t dark:border-gray-700 p-6">
                    <button @click="confirmAddressSelection()" class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-300">
                        Pilih Alamat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Address Modal -->
    <div x-show="showAddAddressForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-50" @click="showAddAddressForm = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden" @click.stop>
                <!-- Add Address Header -->
                <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Tambah Alamat Baru</h3>
                    <button @click="showAddAddressForm = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Add Address Form -->
                <div class="p-6 max-h-96 overflow-y-auto">
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Penerima</label>
                                <input type="text" x-model="newAddress.name" placeholder="Masukkan nama penerima"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nomor Telepon</label>
                                <input type="tel" x-model="newAddress.phone" placeholder="Masukkan nomor telepon"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Label Alamat</label>
                            <div class="flex space-x-2 mb-3">
                                <button type="button" @click="newAddress.type = 'Rumah'" :class="newAddress.type === 'Rumah' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-300">
                                    Rumah
                                </button>
                                <button type="button" @click="newAddress.type = 'Kantor'" :class="newAddress.type === 'Kantor' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-300">
                                    Kantor
                                </button>
                                <button type="button" @click="newAddress.type = 'Lainnya'" :class="newAddress.type === 'Lainnya' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-300">
                                    Lainnya
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat Lengkap</label>
                            <textarea x-model="newAddress.full_address" rows="3" placeholder="Masukkan alamat lengkap..."
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kecamatan</label>
                                <input type="text" x-model="newAddress.district" placeholder="Masukkan kecamatan"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kode Pos</label>
                                <input type="text" x-model="newAddress.postal_code" placeholder="Masukkan kode pos"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Map Integration Placeholder -->
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Pilih lokasi di peta untuk pinpoint yang akurat</p>
                            <button type="button" class="mt-2 text-green-600 dark:text-green-400 text-sm font-medium hover:underline">
                                Buka Peta
                            </button>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" x-model="newAddress.is_primary" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Jadikan alamat utama</label>
                        </div>
                    </form>
                </div>

                <!-- Add Address Footer -->
                <div class="border-t dark:border-gray-700 p-6">
                    <div class="flex space-x-3">
                        <button @click="showAddAddressForm = false" class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                            Batal
                        </button>
                        <button @click="saveNewAddress()" class="flex-1 px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-300">
                            Simpan Alamat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for payment processing -->
<form id="payment-form" style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="amount" id="payment-amount">
    <input type="hidden" name="token_id" id="payment-token">
    <input type="hidden" name="authentication_id" id="payment-auth">
    <input type="hidden" name="channel_code" id="payment-channel">
    <input type="hidden" name="selected_address_id" id="selected-address-id">
</form>
@endsection

@push('scripts')
<script>
// Set Xendit public key globally for checkout.js to use
window.xenditPublicKey = '{{ config("xendivel.public_key") }}';

// Initialize Xendit directly (backup in case checkout.js doesn't load)
if (typeof Xendit !== 'undefined') {
    Xendit.setPublishableKey('{{ config("xendivel.public_key") }}');
}

// Checkout data and functionality
function checkoutData() {
    return {
        showAddressModal: false,
        showAddAddressForm: false,
        activeTab: 'all',
        selectedAddress: {
            id: 1,
            name: 'M Rizal Noerdin',
            phone: '628564349994',
            type: 'Rumah',
            full_address: 'Jl. Poncosiwalan 160 Ngunut, Babadan, Kab. Ponorogo, Jawa Timur',
            is_primary: true,
            is_pinpoint: true
        },
        newAddress: {
            name: '',
            phone: '',
            type: 'Rumah',
            full_address: '',
            district: '',
            postal_code: '',
            is_primary: false
        },
        addresses: [
            {
                id: 1,
                name: 'M Rizal Noerdin',
                phone: '628564349994',
                type: 'Rumah',
                full_address: 'Jl. Poncosiwalan 160 Ngunut, Babadan, Kab. Ponorogo, Jawa Timur',
                is_primary: true,
                is_pinpoint: true
            },
            {
                id: 2,
                name: 'Rizal',
                phone: '628564349994',
                type: 'KOS',
                full_address: 'Jl. Keputih Tim. Jaya II No.30, Keputih, Kec. Sukolilo, Surabaya, Jawa Timur 60111',
                is_primary: false,
                is_pinpoint: true
            }
        ],
        friendsAddresses: [
            // Example friend address - will be populated from backend
            // {
            //     id: 3,
            //     name: 'John Doe',
            //     phone: '628123456789',
            //     type: 'Rumah',
            //     full_address: 'Jl. Example Street No. 123, Jakarta',
            //     is_primary: false,
            //     is_pinpoint: true
            // }
        ],

        selectAddress(address) {
            this.selectedAddress = address;
        },

        confirmAddressSelection() {
            // Close modal and update hidden form field
            this.showAddressModal = false;
            document.getElementById('selected-address-id').value = this.selectedAddress.id;
            
            // Here you would typically make an AJAX call to update the shipping cost
            // this.updateShippingCost(this.selectedAddress.id);
        },

        saveNewAddress() {
            // Validate form
            if (!this.newAddress.name || !this.newAddress.phone || !this.newAddress.full_address) {
                alert('Please fill in all required fields');
                return;
            }

            // Here you would make an AJAX call to save the new address
            // Example:
            // fetch('/api/addresses', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-CSRF-TOKEN': document.querySelector('#csrf-token').value
            //     },
            //     body: JSON.stringify(this.newAddress)
            // })
            // .then(response => response.json())
            // .then(data => {
            //     this.addresses.push(data);
            //     this.selectedAddress = data;
            //     this.showAddAddressForm = false;
            //     this.resetNewAddressForm();
            // });

            // For now, just simulate adding the address
            const newId = Math.max(...this.addresses.map(a => a.id)) + 1;
            const addressToAdd = {
                ...this.newAddress,
                id: newId,
                is_pinpoint: false // Will be true after map selection
            };
            
            this.addresses.push(addressToAdd);
            this.selectedAddress = addressToAdd;
            this.showAddAddressForm = false;
            this.resetNewAddressForm();
        },

        resetNewAddressForm() {
            this.newAddress = {
                name: '',
                phone: '',
                type: 'Rumah',
                full_address: '',
                district: '',
                postal_code: '',
                is_primary: false
            };
        }
    }
}

// Basic initialization check
document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page initialized');
    console.log('Xendit available:', typeof Xendit !== 'undefined');
    console.log('CheckoutManager available:', typeof CheckoutManager !== 'undefined');
});
</script>
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush