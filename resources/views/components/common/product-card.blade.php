{{-- resources/views/components/product-card.blade.php --}}
@props([
    'image' => null,
    'title' => '',
    'price' => '',
    'originalPrice' => null,
    'discount' => null,
    'badge' => null,
    'badgeType' => 'warning', // warning, success, info, danger
    'location' => null,
    'rating' => null,
    'href' => '#',
    'preOrder' => false
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200']) }}>
    {{-- Image Section --}}
    <div class="relative aspect-square overflow-hidden rounded-t-lg bg-gray-50 dark:bg-gray-700">
        {{-- Pre-order Badge --}}
        @if($preOrder)
            <div class="absolute top-2 right-2 z-10">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    PreOrder
                </span>
            </div>
        @endif

        {{-- Product Image --}}
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover">
        @else
            {{-- Placeholder --}}
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif

        {{-- Wishlist/Action Buttons --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if($badge)
                <span class="
                    @if($badgeType === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                    @elseif($badgeType === 'success') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                    @elseif($badgeType === 'info') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                    @elseif($badgeType === 'danger') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                    @endif
                    text-xs font-medium px-2 py-1 rounded-full
                ">
                    {{ $badge }}
                </span>
            @endif
        </div>
    </div>

    {{-- Content Section --}}
    <div class="p-4">
        {{-- Product Title --}}
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2 line-clamp-2 leading-5">
            <a href="{{ $href }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                {{ $title }}
            </a>
        </h3>

        {{-- Price Section --}}
        <div class="mb-2">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $price }}
                </span>
                @if($originalPrice)
                    <span class="text-sm text-gray-500 dark:text-gray-400 line-through">
                        {{ $originalPrice }}
                    </span>
                @endif
            </div>
            
            @if($discount)
                <span class="text-xs text-red-600 dark:text-red-400 font-medium">
                    {{ $discount }}
                </span>
            @endif
        </div>

        {{-- Location & Rating --}}
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            @if($location)
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $location }}
                </span>
            @endif
            
            @if($rating)
                <div class="flex items-center gap-1">
                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>{{ $rating }}</span>
                </div>
            @endif
        </div>

        {{-- Action Button (Optional) --}}
        {{ $slot }}
    </div>
</div>