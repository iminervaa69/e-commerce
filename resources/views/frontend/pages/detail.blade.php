@php
    $images = [
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
        ['src' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
    ]
@endphp


@extends('frontend.layouts.main')

@section('title')
VGA POWERCOLOR RX 6600 XT 8GB RX 6600XT 8GB
@endsection

@section('content')
<div class="p-4 mt-14 dark:bg-gray-900 min-h-screen">
    
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 lg:col-span">
            <x-common.breadcrumb :items="[
                ['label' => 'Komputer & Laptop', 'href' => '#'],
                ['label' => 'Komponen Komputer', 'href' => '#'],  
                ['label' => 'VGA Card', 'href' => '#'],
                ['label' => 'VGA POWERCOLOR RX 6600 XT 8GB']
            ]" class="mb-6" />
            <div class="mx-auto max-w-screen-xl grid grid-cols-1 lg:grid-cols-10 gap-7  mt-10">
                <div class="lg:col-span-7">
                    <div class="grid grid-cols-1 lg:grid-cols-7 gap-8 mb-8">
                        <div class="lg:col-span-3">
                            <x-common.image-gallery 
                                :main-image="'storage/photos/1/placeholder.jpg'"
                                :thumbnails="[
                                    ['url' => 'storage/photos/1/placeholder.jpg','alt' => 'Exusiai'],
                                ]"
                                product-name="RADEON RX 6600XT"
                            />
                        </div>

                        <div class="lg:col-span-4">
                            <x-common.info-card 
                                title="VGA POWERCOLOR RX 6600 XT 8GB RX 6600XT 8GB"
                                subtitle="AXRX 6600XT 8GBD6-3DH"
                                price="Rp3.300.000"
                                :rating="5"
                                :total-ratings="7"
                                condition="Bekas"
                                :min-order="1"
                                :tags="['Semua Etalase']"
                                description="rx 6600xt hellhound"
                                :stock="10"
                                :preorder-time="10"
                            />

                            <div class="border-black mt-4">
                                <x-common.seller-info 
                                    seller-name="46comp"
                                    :seller-rating="5"
                                    seller-location="Kota Administratif Jakarta Pusat"
                                    :is-online="true"
                                    response-time="5 jam pesanan diproses"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 ">
                        <div class="lg:col-span-2">
                            <x-common.tabs 
                                :tabs="['Detail', 'Spesifikasi', 'Info Penting']"
                                active-tab="Detail"
                                details="<p>RX 6600XT Hellhound dari PowerColor adalah kartu grafis gaming yang powerful dengan performa tinggi untuk gaming 1080p dan 1440p. Dilengkapi dengan 8GB GDDR6 memory dan arsitektur RDNA 2 terbaru dari AMD.</p>
                                <h3>Fitur Utama:</h3>
                                <ul>
                                    <li>8GB GDDR6 Memory</li>
                                    <li>AMD RDNA 2 Architecture</li>
                                    <li>Ray Tracing Support</li>
                                    <li>AMD FidelityFX Super Resolution</li>
                                    <li>Dual Fan Cooling System</li>
                                </ul>"
                                :specifications="[
                                    'Graphics' => [
                                        'GPU' => 'AMD Radeon RX 6600 XT',
                                        'Architecture' => 'RDNA 2',
                                        'Process Node' => '7nm',
                                        'Transistors' => '11.06 billion',
                                        'Die Size' => '237 mm²'
                                    ],
                                    'Memory' => [
                                        'Memory Size' => '8 GB',
                                        'Memory Type' => 'GDDR6',
                                        'Memory Bus' => '128 bit',
                                        'Bandwidth' => '256 GB/s'
                                    ],
                                    'Clock Speeds' => [
                                        'Base Clock' => '1968 MHz',
                                        'Game Clock' => '2359 MHz',
                                        'Boost Clock' => '2589 MHz'
                                    ],
                                    'Display' => [
                                        'Maximum Resolution' => '7680 x 4320',
                                        'HDMI' => '1x HDMI 2.1',
                                        'DisplayPort' => '3x DisplayPort 1.4a'
                                    ]
                                ]"
                                important-info="<div class='space-y-4'>
                                    <div class='bg-red-50 dark:bg-gray-800 border-l-4 border-red-400 p-4'>
                                        <h4 class='font-semibold text-red-800 dark:text-red-500'>Persyaratan Sistem:</h4>
                                        <ul class='text-red-700 dark:text-red-400 text-sm mt-2'>
                                            <li>• PSU minimum 500W dengan konektor 8-pin PCIe</li>
                                            <li>• Slot PCIe x16 yang tersedia</li>
                                            <li>• Clearance case minimum 240mm</li>
                                        </ul>
                                    </div>
                                    <div class='bg-blue-50 dark:bg-gray-800 border-l-4 border-blue-400 p-4'>
                                        <h4 class='font-semibold text-blue-800 dark:text-blue-500'>Garansi:</h4>
                                        <p class='text-blue-700 dark:text-blue-400 text-sm'>Garansi resmi 2 tahun dari distributor PowerColor Indonesia</p>
                                    </div>
                                </div>"
                            />
                        </div>
                    </div>

                    <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-4 mt-10 uppercase tracking-wide">
                        ULASAN PEMBELI
                    </h3>
                    <div>
                        <x-review-card 
                            title="ULASAN PEMBELI"
                            :average-rating="5.0"
                            :max-rating="5"
                            satisfaction-text="100% pembeli merasa puas"
                            :total-reviews="13"
                            :total-comments="11"
                            :ratings="[
                                5 => 13,
                                4 => 0,
                                3 => 0,
                                2 => 0,
                                1 => 0
                            ]"
                        />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-7 gap-8 mb-8">
                        <div class="lg:col-span-2 mt-5">
                            <x-review-filter />
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <x-common.purchase-section 
                        :stock="10"
                        :price="3300000"
                        :min-order="1"
                        product-id="1"
                        :show-notes="true"
                    />
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 mt-10">Lainnya dari toko ini</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
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
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 mt-10">Pilihan Lainnya untuk anda</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
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
            </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateQuantity();
    
    console.log('Product page loaded');
});
</script>
@endsection