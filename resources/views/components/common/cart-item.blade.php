{{-- Cart Item Component --}}
@props([
    'id',
    'name' => '',
    'price' => 0.00,
    'originalPrice' => null,
    'quantity' => 1,
    'image' => null,
    'inStock' => true,
    'productAttributes' => [],
    'storeName' => '',
])

<div class="cart-item p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200" data-item-id="{{ $id }}" data-store="{{ $storeName }}">
    <div class="flex gap-4">
        <!-- Checkbox -->
        <div class="flex-shrink-0 pt-1">
            <input type="checkbox" 
                   class="item-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                   data-item-id="{{ $id }}" 
                   data-store="{{ $storeName }}">
        </div>

        <!-- Product Image -->
        <div class="flex-shrink-0">
            <img src="{{ $image }}" alt="{{ $name }}" class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg border dark:border-gray-600">
        </div>

        <!-- Product Details -->
        <div class="flex-grow">
            <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                <div class="flex-grow">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $name }}</h3>
                    
                    @if(isset($productAttributes) && count($productAttributes) > 0)
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach($productAttributes as $key => $value)
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">{{ $key }}:</span> {{ $value }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    <div class="mb-3">
                        @if($inStock)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            In Stock
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Out of Stock
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl font-bold text-gray-900 dark:text-white" data-item-price="{{ $price }}">${{ number_format($price, 2) }}</span>
                        @if(isset($originalPrice) && $originalPrice)
                        <span class="text-sm text-gray-500 dark:text-gray-400 line-through">${{ number_format($originalPrice, 2) }}</span>
                        <span class="text-sm font-medium text-red-600 dark:text-red-400">
                            {{ round((($originalPrice - $price) / $originalPrice) * 100) }}% off
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Quantity and Actions -->
                <div class="flex flex-col items-end gap-3 min-w-[120px]">
                    <!-- Quantity Control -->
                    <div class="flex items-center border dark:border-gray-600 rounded-lg">
                        <button type="button" 
                                class="quantity-btn p-2 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                data-action="decrease"
                                {{ $quantity <= 1 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        
                        <input type="number" 
                               value="{{ $quantity }}" 
                               min="1" 
                               max="99"
                               class="quantity-input w-16 px-2 py-1 text-center border-0 bg-transparent text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                               data-item-id="{{ $id }}">
                        
                        <button type="button" 
                                class="quantity-btn p-2 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                data-action="increase">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Price Display -->
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            ${{ number_format($price * $quantity, 2) }}
                        </div>
                        @if($quantity > 1)
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            ${{ number_format($price, 2) }} each
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <!-- Move to Wishlist -->
                        <button type="button" 
                                class="move-to-wishlist text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 font-medium flex items-center gap-1 transition-colors"
                                data-item-id="{{ $id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Save
                        </button>

                        <!-- Remove Item -->
                        <button type="button" 
                                class="remove-item text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium flex items-center gap-1 transition-colors"
                                data-item-id="{{ $id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>