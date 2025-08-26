@extends('frontend.layouts.main')

@section('title')
Payment Success
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300 p-12">
                <!-- Success Icon -->
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Payment Successful!</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">Thank you for your order. We've received your payment and will process your order shortly.</p>
                
                <!-- Order Details -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="text-left">
                            <span class="text-gray-600 dark:text-gray-400">Order ID:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">#{{ $orderId ?? 'ORD123456' }}</span>
                        </div>
                        <div class="text-left md:text-right">
                            <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${{ $amount ?? '117.71' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-x-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-300">
                        Continue Shopping
                    </a>
                    <a href="#" class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-300">
                        View Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection