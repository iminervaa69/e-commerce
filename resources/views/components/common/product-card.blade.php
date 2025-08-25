{{-- resources/views/components/product-card.blade.php --}}
@props([
    'image' => null,
    'title' => '',
    'price' => '',
    'originalPrice' => null,
    'discount' => null,
    'storeName' => null,
    'badge' => null,
    'badgeType' => 'warning',
    'location' => null,
    'rating' => null,
    'href' => '#',
    'preOrder' => false
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200']) }}>
    <a href="{{ $href }}">
    <div  class="relative aspect-square overflow-hidden rounded-t-lg bg-gray-50 dark:bg-gray-700">
        @if($preOrder)
            <div class="absolute top-2 right-2 z-10">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    PreOrder
                </span>
            </div>
        @endif

        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif

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

    <div class="p-4">

        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2 line-clamp-2 leading-5">
            {{ $title }}
        </h3>

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

        <div class="items-center text-xs text-gray-500 dark:text-gray-400">
            @if($storeName)
                <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" 
                        class="w-3 h-3 lucide lucide-store-icon lucide-store">
                        <path d="M15 21v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5"/><path d="M17.774 10.31a1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.451 0 1.12 1.12 0 0 0-1.548 0 2.5 2.5 0 0 1-3.452 0 1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.77-3.248l2.889-4.184A2 2 0 0 1 7 2h10a2 2 0 0 1 1.653.873l2.895 4.192a2.5 2.5 0 0 1-3.774 3.244"/><path d="M4 10.95V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8.05"/>
                    </svg>
                    <span>{{ $storeName }}</span>
                </div>
            @endif
            @if($location)
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" 
                        class="w-5 h-5 lucide lucide-store-icon lucide-store">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $location }}
                </span>
            @endif
        </div>

        {{ $slot }}
    </div>
    </a>
</div>