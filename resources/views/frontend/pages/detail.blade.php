@extends('frontend.layouts.main')

@section('title')
{{ $productInfo['title'] }}
@endsection

@section('content')
<div class="p-4 mt-14 dark:bg-gray-900 min-h-screen">
    <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8 lg:col-span">
        <x-common.breadcrumb :items="$breadcrumbs" class="mb-6" />
        
        <div class="mx-auto max-w-screen-xl grid grid-cols-1 lg:grid-cols-10 gap-7 mt-10">
            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 lg:grid-cols-7 gap-8 mb-8">
                    <div class="lg:col-span-3">
                        <x-common.image-gallery 
                            :main-image="$productImages['main']"
                            :thumbnails="$productImages['thumbnails']"
                            :product-name="$productInfo['title']"
                        />
                    </div>

                    <div class="lg:col-span-4">
                        <x-common.info-card 
                            :title="$productInfo['title']"
                            :subtitle="$productInfo['subtitle']"
                            :price="$productInfo['price']"
                            :rating="$productInfo['rating']"
                            :total-ratings="$productInfo['total_ratings']"
                            :condition="$productInfo['condition']"
                            :min-order="$productInfo['min_order']"
                            :tags="$productInfo['tags']"
                            :description="$productInfo['description']"
                            :stock="$productInfo['stock']"
                            :preorder-time="$productInfo['preorder_time']"
                            :variants="$productVariants"
                        />

                        <div class="border-black mt-4">
                            <x-common.seller-info 
                                :seller-name="$sellerInfo['name']"
                                :seller-rating="$sellerInfo['rating']"
                                :seller-location="$sellerInfo['location']"
                                :is-online="$sellerInfo['is_online']"
                                :response-time="$sellerInfo['response_time']"
                            />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    <div class="lg:col-span-2">
                        <x-common.tabs 
                            :tabs="['Detail', 'Spesifikasi', 'Info Penting']"
                            active-tab="Detail"
                            :details="$product->description ?? 'No detailed description available.'"
                            :specifications="is_string($product->specifications) ? json_decode($product->specifications, true) ?? [] : ($product->specifications ?? [])"
                            :important-info="$product->important_info ?? 'No important information available.'"
                        />
                    </div>
                </div>

                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-4 mt-10 uppercase tracking-wide">
                    ULASAN PEMBELI
                </h3>
                <div>
                    <x-review-card 
                        title="ULASAN PEMBELI"
                        :average-rating="$reviewsData['average_rating']"
                        :max-rating="5"
                        satisfaction-text="{{ $reviewsData['satisfaction_rate'] }}% pembeli merasa puas"
                        :total-reviews="$reviewsData['total_reviews']"
                        :total-comments="$reviewsData['total_comments']"
                        :ratings="$reviewsData['ratings_breakdown']"
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
                    :stock="$selectedVariant ? $selectedVariant['stock'] : $productInfo['stock']"
                    :price="$selectedVariant ? $selectedVariant['price'] : $product->min_price"
                    :min-order="$productInfo['min_order']"
                    :product-id="$product->id"
                    :product-variant-id="$selectedVariant ? $selectedVariant['id'] : null"
                    :show-notes="true"
                />
            </div>
        </div>

        @if($relatedProducts->isNotEmpty())
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 mt-10">Lainnya dari toko ini</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach ($relatedProducts as $relatedProduct)
                    <x-common.product-card 
                        :image="$relatedProduct['image']"
                        :title="$relatedProduct['name']"
                        :price="$relatedProduct['price']"
                        :badge="$relatedProduct['badge']"
                        :badge-type="$relatedProduct['badge_type']"
                        :location="$relatedProduct['location']"
                        :rating="$relatedProduct['rating']"
                        :href="$relatedProduct['href']"
                        :store-name="$relatedProduct['store_name']"
                        class="hover:scale-105 transition-transform duration-200"
                    />
                @endforeach
            </div>
        @endif

        @if($recommendedProducts->isNotEmpty())
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 mt-10">Pilihan Lainnya untuk anda</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach ($recommendedProducts as $recommendedProduct)
                    <x-common.product-card 
                        :image="$recommendedProduct['image']"
                        :title="$recommendedProduct['name']"
                        :price="$recommendedProduct['price']"
                        :badge="$recommendedProduct['badge']"
                        :badge-type="$recommendedProduct['badge_type']"
                        :location="$recommendedProduct['location']"
                        :rating="$recommendedProduct['rating']"
                        :href="$recommendedProduct['href']"
                        :store-name="$recommendedProduct['store_name']"
                        class="hover:scale-105 transition-transform duration-200"
                    />
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
let currentSelectedVariant = @json($selectedVariant);

function updatePurchaseSection(variantData) {
    currentSelectedVariant = variantData;
    
        if (typeof productVariantId !== 'undefined') {
            productVariantId = variantData.id;
        }
        
        const priceElements = document.querySelectorAll('#subtotal');
        if (priceElements.length > 0 && typeof updateQuantity === 'function') {
            bPrice = variantData.price;
            updateQuantity();
        }
        
        const stockElements = document.querySelectorAll('span:contains("Sisa")');
        stockElements.forEach(el => {
            if (el.textContent.includes('Sisa')) {
                el.textContent = `Sisa ${variantData.stock}`;
            }
        });
}

document.addEventListener('variantSelected', function(event) {
    updatePurchaseSection(event.detail);
});

document.addEventListener('DOMContentLoaded', function() {
    updateQuantity();
    console.log('Product page loaded with variant:', currentSelectedVariant);
});
</script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateQuantity();
    console.log('Product page loaded');
});
</script>
@endsection