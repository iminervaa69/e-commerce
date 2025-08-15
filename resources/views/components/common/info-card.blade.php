@props([
    'title',
    'subtitle' => '',
    'price',
    'originalPrice' => null,
    'rating' => 0,
    'totalRatings' => 0,
    'condition' => 'Baru',
    'minOrder' => 1,
    'tags' => [],
    'description' => '',
    'stock' => 0,
    'preorderTime' => null,
])

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-gray-600 dark:text-gray-300 mb-2">{{ $subtitle }}</p>
        @endif
        
        @if($rating > 0)
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium dark:text-gray-400">Terjual {{ number_format($totalRatings) }}</span>
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    <span class="text-sm text-gray-600 ml-1">{{ $rating }} ({{ $totalRatings }} rating)</span>
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center space-x-3">
        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $price }}</span>
        @if($originalPrice)
            <span class="text-lg text-gray-500 line-through">{{ $originalPrice }}</span>
        @endif
    </div>

    <div class="py-3 space-y-1 border-t border-b border-gray-400 dark:border-gray-700">
        @isset($condition)
            <div class="flex justify-between">
                <span class="text-gray-900 dark:text-gray-200">Kondisi:</span>
                <span class="font-medium dark:text-white">{{ $condition }}</span>
            </div>  
        @endisset
        @isset($minOrder)
        <div class="flex justify-between">
            <span class="text-gray-900 dark:text-gray-200">Min. Pemesanan:</span>
            <span class="font-medium dark:text-white">{{ $minOrder }} Buah</span>
        </div>
        @endisset()
        @isset($preorderTime)
        <div class="flex justify-between">
            <span class="text-gray-900 dark:text-gray-200">Waktu Preorder:</span>
            <span class="font-medium dark:text-white">{{ $preorderTime }} Hari</span>
        </div>
        @endisset()
        @if(count($tags) > 0)
            <div class="flex justify-between">
                <span class="text-gray-900 dark:text-gray-200">Etalase:</span>
                <div class="flex flex-wrap gap-1">
                    @foreach($tags as $tag)
                        <span class="px-2 py-1 border border-green-400 text-green-400 text-xs rounded">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>