@extends('frontend.layouts.main')

@section('title')
Home
@endsection

@section('content')
<div class="p-4 h-100000 mt-14 dark:bg-gray-900">
    {{-- Use carousel images from controller --}}
    <x-common.carousel
        id="dashboard-carousel"
        :items="$carouselImages"
        height="h-64 md:h-80"
        :autoPlay="true"
        :autoPlayTimeout="4000"
        animateOut="slideOutLeft"
        animateIn="slideInRight"
        :smartSpeed="600"
        parentClass="mb-9 mt-7"
    />

    {{-- Product Grid using data from controller --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach ($products as $product)
            <x-common.product-card 
                :image="$product['image']"
                :title="$product['name']"
                :price="$product['price_range']"
                :badge="$product['badge']"
                :badge-type="$product['badge_type']"
                :location="$product['location']"
                :rating="$product['rating']"
                :href="$product['href']"
                :pre-order="$product['is_preorder']"
                class="hover:scale-105 transition-transform duration-200"
            >
                {{-- Action button based on preorder status --}}
                <div class="mt-3">
                    @if($product['is_preorder'])
                        <button class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors dark:bg-orange-500 dark:hover:bg-orange-600">
                            Pre-order Now
                        </button>
                    @else
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors dark:bg-blue-500 dark:hover:bg-blue-600">
                            Add to Cart
                        </button>
                    @endif
                </div>
            </x-common.product-card>
        @endforeach
    </div>
</div>
@endsection

@section('insert-scripts')
@endsection