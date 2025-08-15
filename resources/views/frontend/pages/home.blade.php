@extends('frontend.layouts.main')

@php
    $images = [
        ['src' => 'storage/photos/1/exusiai-1.png','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-1.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-2.png','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-2.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-3.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-4.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-5.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-6.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-7.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-8.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-9.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-10.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-11.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-12.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-13.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-14.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-15.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-16.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/exusiai-17.jpg','alt' => 'Exusiai'],
    ]
@endphp

@section('title')
Dashboard
@endsection

@section('content')
<div class="p-4 h-100000 mt-14 dark:bg-gray-900">
    <x-common.carousel
        id="dashboard-carousel"
        :items="$images"
        height="h-64 md:h-80"
        :autoPlay="true"
        :autoPlayTimeout="4000"
        animateOut="slideOutLeft"
        animateIn="slideInRight"
        :smartSpeed="600"
        parentClass="mb-9 mt-7"
    />

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        {{-- Product Card 1 - ASUS TUF Gaming --}}
        @foreach ($images as $image)
            <x-common.product-card 
                image="{{ $image['src'] }}"
                title="ASUS TUF Gaming Radeon RX 6700 XT OC 12GB GDDR6"
                price="Rp6.831.000"
                badge="Hemat s.d. 6% Pakai Bonus"
                badge-type="warning"
                location="Jakarta Pusat"
                rating="4.8"
                href="/product/asus-tuf-gaming"
                class="hover:scale-105 transition-transform duration-200"
            >
            {{-- Optional action button --}}
            <div class="mt-3">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors dark:bg-blue-500 dark:hover:bg-blue-600">
                    Add to Cart
                </button>
            </div>
            </x-common.product-card>
        @endforeach
        
        <x-common.product-card 
            image="storage/photos/1/exusiai-1.png"
            title="ASUS TUF Gaming Radeon RX 6700 XT OC 12GB GDDR6"
            price="Rp6.831.000"
            badge="Hemat s.d. 6% Pakai Bonus"
            badge-type="warning"
            location="Jakarta Pusat"
            rating="4.8"
            href="/product/asus-tuf-gaming"
            class="hover:scale-105 transition-transform duration-200"
        >
            {{-- Optional action button --}}
            <div class="mt-3">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors dark:bg-blue-500 dark:hover:bg-blue-600">
                    Add to Cart
                </button>
            </div>
        </x-common.product-card>

        {{-- Product Card 2 - PowerColor Radeon --}}
        <x-common.product-card 
            image="storage/photos/1/exusiai-6.jpg"
            title="Powercolor Radeon Rx 6700 XT Red Dragon 12GB GDDR6"
            price="Rp8.494.658"
            location="Jakarta Pusat"
            rating="4.9"
            href="/product/powercolor-radeon"
            :pre-order="true"
            class="hover:scale-105 transition-transform duration-200"
        >
            <div class="mt-3">
                <button class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors dark:bg-orange-500 dark:hover:bg-orange-600">
                    Pre-order Now
                </button>
            </div>
        </x-common.product-card>
    </div>
</div>
@endsection

@section('insert-scripts')
@endsection