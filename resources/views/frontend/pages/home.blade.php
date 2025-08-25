@extends('frontend.layouts.main')

@section('title')
Home
@endsection

@section('content')
<div class="p-4 h-100000 mt-14 dark:bg-gray-900">
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

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach ($products as $product)
            <x-common.product-card 
                :image="$product['image']"
                :title="$product['name']"
                :price="$product['price']"
                :badge="$product['badge']"
                :badge-type="$product['badge_type']"
                :location="$product['location']"
                :rating="$product['rating']"
                :href="$product['href']"
                :store-name="$product['store_name']"
                :pre-order="$product['is_preorder']"
                class="hover:scale-105 transition-transform duration-200"
            />
            <x-common.product-card 
                :image="$product['image']"
                :title="$product['name']"
                :price="$product['price']"
                :badge="$product['badge']"
                :badge-type="$product['badge_type']"
                :location="$product['location']"
                :rating="$product['rating']"
                :href="$product['href']"
                :store-name="$product['store_name']"
                :pre-order="$product['is_preorder']"
                class="hover:scale-105 transition-transform duration-200"
            />
        @endforeach
    </div>
</div>
@endsection

@section('insert-scripts')
<script>
// Function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Function to update cart count in header
function updateCartCount(count) {
    const cartCountElement = document.querySelector('[data-cart-count]');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.classList.add('animate-pulse');
        setTimeout(() => {
            cartCountElement.classList.remove('animate-pulse');
        }, 1000);
    }
}
</script>
@endsection