@extends('frontend.auth.app')

@section('title')
Confirm Password
@endsection

@section('content')
<div class="min-h-screen bg-gray-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo -->
        <div class="text-center">
            <div class="mx-auto h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-4V9a2 2 0 10-4 0v2m-2 0v6a2 2 0 002 2h4a2 2 0 002-2v-6a2 2 0 00-2-2H10z"/>
                </svg>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Secure Area</h1>
            <p class="text-gray-400">
                This is a secure area of the application. Please confirm your password before continuing.
            </p>
        </div>

        <!-- Confirm Password Form -->
        <form class="space-y-6" action="{{ route('password.confirm') }}" method="POST">
            @csrf

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                    Password
                </label>
                <input id="password"
                       name="password"
                       type="password"
                       autocomplete="current-password"
                       required
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Button -->
            <div>
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02]">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
    <div class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ $errors->first() }}
    </div>
@endif
@endsection
