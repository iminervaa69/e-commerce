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
    'variants' => [],
    'selectedVariants' => [],
])

<div class="space-y-6">
    <div>
        <span id="title-text" class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $title }}</span>
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
                    <span class="text-sm text-gray-600 ml-1 dark:text-gray-300">{{ $rating }} ({{ $totalRatings }} rating)</span>
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center space-x-3">
        <span id="display-price" class="text-3xl font-bold text-gray-900 dark:text-white">{{ $price }}</span>
        @if($originalPrice)
            <span id="display-original-price" class="text-lg text-gray-500 line-through">{{ $originalPrice }}</span>
        @endif
        <span id="price-adjustment-display" class="text-lg font-medium text-green-600 dark:text-green-400 hidden"></span>
    </div>

    @if(count($variants) > 0)
    <div class="space-y-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">Pilih Varian</h3>
        
        @foreach($variants as $variantType => $variant)
        <div class="space-y-3">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ $variant['label'] }}
                </label>
                @if($variant['required'] ?? false)
                    <span class="text-red-500 text-sm">*</span>
                @endif
            </div>
            
            @if($variantType === 'color' && isset($variant['options'][0]['color_code']))
                <div class="flex flex-wrap gap-2">
                    @foreach($variant['options'] as $option)
                    <div class="relative">
                        <input 
                            type="radio" 
                            id="variant_{{ $variantType }}_{{ $option['id'] }}"
                            name="variant_{{ $variantType }}"
                            value="{{ $option['id'] }}"
                            data-variant-type="{{ $variantType }}"
                            data-variant-value="{{ $option['value'] }}"
                            data-price-diff="{{ $option['price_diff'] ?? 0 }}"
                            data-stock="{{ $option['stock'] ?? 0 }}"
                            class="sr-only variant-option"
                            @if(($selectedVariants[$variantType] ?? null) == $option['id']) checked @endif
                            @if(($option['stock'] ?? 0) <= 0) disabled @endif
                            onchange="updateVariantSelection('{{ $variantType }}', '{{ $option['id'] }}', this)"
                        >
                        <label 
                            for="variant_{{ $variantType }}_{{ $option['id'] }}" 
                            class="flex flex-col items-center p-2 border-2 rounded-lg cursor-pointer transition-all
                                   {{ ($option['stock'] ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed border-gray-200 dark:border-gray-600' : 'border-gray-200 dark:border-gray-600 hover:border-cyan-400' }}
                                   {{ ($selectedVariants[$variantType] ?? null) == $option['id'] ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20' : '' }}"
                        >
                            <div 
                                class="w-8 h-8 rounded-full border-2 border-white shadow-sm mb-1"
                                style="background-color: {{ $option['color_code'] ?? '#gray' }}"
                            ></div>
                            <span class="text-xs text-center dark:text-white">{{ $option['label'] }}</span>
                            {{-- @if(($option['stock'] ?? 0) <= 0)
                                <span class="text-xs text-red-500 mt-1">Habis</span>
                            @elseif(($option['price_diff'] ?? 0) != 0)
                                <span class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    {{ ($option['price_diff'] ?? 0) > 0 ? '+' : '' }}Rp{{ number_format($option['price_diff'] ?? 0) }}
                                </span>
                            @endif --}}
                        </label>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($variant['options'] as $option)
                    <div class="relative">
                        <input 
                            type="radio" 
                            id="variant_{{ $variantType }}_{{ $option['id'] }}"
                            name="variant_{{ $variantType }}"
                            value="{{ $option['id'] }}"
                            data-variant-type="{{ $variantType }}"
                            data-variant-value="{{ $option['value'] }}"
                            data-price-diff="{{ $option['price_diff'] ?? 0 }}"
                            data-stock="{{ $option['stock'] ?? 0 }}"
                            class="sr-only variant-option"
                            @if(($selectedVariants[$variantType] ?? null) == $option['id']) checked @endif
                            @if(($option['stock'] ?? 0) <= 0) disabled @endif
                            onchange="updateVariantSelection('{{ $variantType }}', '{{ $option['id'] }}', this)"
                        >
                        <label 
                            for="variant_{{ $variantType }}_{{ $option['id'] }}" 
                            class="flex flex-col items-center px-4 py-2 border-2 rounded-lg cursor-pointer transition-all min-w-[60px]
                                   {{ ($option['stock'] ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700' : 'border-gray-200 dark:border-gray-600 hover:border-cyan-400 bg-white dark:bg-gray-800' }}
                                   {{ ($selectedVariants[$variantType] ?? null) == $option['id'] ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20' : '' }}"
                        >
                            <span class="text-sm font-medium text-center dark:text-white">{{ $option['label'] }}</span>
                            {{-- @if(($option['stock'] ?? 0) <= 0)
                                <span class="text-xs text-red-500 mt-1">Habis</span>
                            @elseif(($option['price_diff'] ?? 0) != 0)
                                <span class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    {{ ($option['price_diff'] ?? 0) > 0 ? '+' : '' }}Rp{{ number_format($option['price_diff'] ?? 0) }}
                                </span>
                            @endif --}}
                        </label>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach
        
        <div id="variant-summary" class="pt-3 border-t border-gray-200 dark:border-gray-600 hidden">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">Varian dipilih:</span>
                <span id="selected-variants-text"></span>
            </div>
        </div>
    </div>
    @endif

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
        <div class="flex justify-between">
            <span class="text-gray-900 dark:text-gray-200">Stok:</span>
            <span id="stock-display" class="font-medium dark:text-white">{{ $stock }} tersedia</span>
        </div>
        @isset($preorderTime)
        <div class="flex justify-between">
            <span class="text-gray-900 dark:text-gray-200">Waktu Preorder:</span>
            <span class="font-medium dark:text-white">{{ $preorderTime }} Hari</span>
        </div>
        @endisset()
        @if(count($tags) > 0)
            <div class="flex justify-between">
                <span class="text-gray-900 dark:text-gray-200">Tag:</span>
                <div class="flex flex-wrap gap-1">
                    @foreach($tags as $tag)
                        <span class="px-2 py-1 border border-green-400 text-green-400 text-xs rounded">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
let selectedVariants = @json($selectedVariants);
let variantPriceDiff = 0;
const basePrice = {{ preg_replace('/[^0-9]/', '', $price) }}; // Extract numeric value
const basePriceFormatted = "{{ $price }}";
const baseStock = {{ $stock }};

function updateVariantSelection(variantType, optionId, element) {
    selectedVariants[variantType] = optionId;
    
    const priceDiff = parseInt(element.dataset.priceDiff) || 0;
    const variantStock = parseInt(element.dataset.stock) || 0;
    
    calculateTotalPriceAdjustment();
    
    updatePriceDisplay();
    updateStockDisplay(variantStock);
    updateVariantSummary();
    
    if (typeof window.onVariantChange === 'function') {
        window.onVariantChange(selectedVariants, variantPriceDiff, variantStock);
    }
    
    console.log('Selected variants:', selectedVariants);
    console.log('Price adjustment:', variantPriceDiff);
    console.log('Variant stock:', variantStock);
}

function calculateTotalPriceAdjustment() {
    variantPriceDiff = 0;
    
    document.querySelectorAll('.variant-option:checked').forEach(input => {
        variantPriceDiff += parseInt(input.dataset.priceDiff) || 0;
    });
}

function updatePriceDisplay() {
    const priceDisplay = document.getElementById('display-price');
    const priceAdjustmentDisplay = document.getElementById('price-adjustment-display');
    
    if (priceDisplay) {
        const newPrice = basePrice + variantPriceDiff;
        priceDisplay.textContent = 'Rp' + newPrice.toLocaleString('id-ID');
        
        if (variantPriceDiff !== 0 && priceAdjustmentDisplay) {
            priceAdjustmentDisplay.textContent = (variantPriceDiff > 0 ? '+' : '') + 'Rp' + variantPriceDiff.toLocaleString('id-ID');
            priceAdjustmentDisplay.classList.remove('hidden');
        } else if (priceAdjustmentDisplay) {
            priceAdjustmentDisplay.classList.add('hidden');
        }
    }
}

function updateStockDisplay(variantStock) {
    const stockDisplay = document.getElementById('stock-display');
    if (stockDisplay) {
        const displayStock = variantStock || baseStock;
        const stockText = displayStock > 0 ? `${displayStock} tersedia` : 'Stok habis';
        const stockClass = displayStock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500';
        
        stockDisplay.textContent = stockText;
        stockDisplay.className = `font-medium ${stockClass}`;
    }
}

function updateVariantSummary() {
    const summaryDiv = document.getElementById('variant-summary');
    const selectedText = document.getElementById('selected-variants-text');
    const titleElement = document.getElementById('title-text');
    
    if (!summaryDiv || !selectedText || !titleElement) return;
    
    if (!titleElement.dataset.originalTitle) {
        titleElement.dataset.originalTitle = titleElement.textContent.trim();
    }
    
    const originalTitle = titleElement.dataset.originalTitle;
    let variantTexts = [];
    let variantTitles = [];
    
    document.querySelectorAll('.variant-option:checked').forEach(input => {
        const label = document.querySelector(`label[for="${input.id}"]`);
        if (label) {
            const variantType = input.dataset.variantType;
            const labelSpan = label.querySelector('span');
            const labelText = labelSpan ? labelSpan.textContent.trim() : label.textContent.trim();
            
            const formattedVariantType = variantType.charAt(0).toUpperCase() + variantType.slice(1);
            variantTexts.push(`${formattedVariantType} - ${labelText}`);
            variantTitles.push(labelText);
        }
    });
    
    if (variantTexts.length > 0) {
        summaryDiv.classList.remove('hidden');
        selectedText.textContent = variantTexts.join(', ');
        titleElement.textContent = `${originalTitle} ${variantTitles.join(' - ')}`;
    } else {
        summaryDiv.classList.add('hidden');
        titleElement.textContent = originalTitle; 
    }
}

function getSelectedVariants() {
    return {
        variants: selectedVariants,
        priceDiff: variantPriceDiff,
        finalPrice: basePrice + variantPriceDiff
    };
}

function validateRequiredVariants() {
    const requiredVariants = [];
    @foreach($variants as $variantType => $variant)
        @if($variant['required'] ?? false)
            requiredVariants.push('{{ $variantType }}');
        @endif
    @endforeach
    
    for (let variantType of requiredVariants) {
        if (!selectedVariants[variantType]) {
            return {
                valid: false,
                missing: variantType
            };
        }
    }
    return { valid: true };
}

function getCurrentVariantStock() {
    const checkedInputs = document.querySelectorAll('.variant-option:checked');
    if (checkedInputs.length > 0) {
        let minStock = baseStock;
        checkedInputs.forEach(input => {
            const stock = parseInt(input.dataset.stock) || 0;
            minStock = Math.min(minStock, stock);
        });
        return minStock;
    }
    return baseStock;
}

document.addEventListener('DOMContentLoaded', function() {
    calculateTotalPriceAdjustment();
    updatePriceDisplay();
    updateVariantSummary();
    
    const preSelectedStock = getCurrentVariantStock();
    if (document.querySelectorAll('.variant-option:checked').length > 0) {
        updateStockDisplay(preSelectedStock);
    }
});

window.productVariants = {
    getSelectedVariants,
    validateRequiredVariants,
    getCurrentVariantStock
};
</script>