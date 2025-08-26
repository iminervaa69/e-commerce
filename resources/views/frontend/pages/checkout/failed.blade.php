@extends('frontend.layouts.main')

@section('title')
Payment Failed
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700 transition-colors duration-300 p-12">
                <!-- Error Icon -->
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Payment Failed</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">We couldn't process your payment. Please try again or use a different payment method.</p>
                
                <!-- Error Details -->
                @if(isset($errorMessage))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-8">
                    <p class="text-red-800 dark:text-red-400 text-sm">{{ $errorMessage }}</p>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="space-x-4">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-300">
                        Try Again
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-300">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection