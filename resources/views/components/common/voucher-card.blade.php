{{-- resources/views/components/voucher-card.blade.php --}}
@props([
    'voucher' => [],
    'isSelected' => false,
    'onClick' => null,
    'isExpired' => false,
    'isUnavailable' => false
])

@php
    $cardClasses = 'relative flex items-center p-3 border rounded-lg transition-all duration-200 cursor-pointer ';
    $cardClasses .= $isSelected 
        ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-900/20 dark:border-cyan-400' 
        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500';
    
    $cardClasses .= $isExpired || $isUnavailable 
        ? ' opacity-50 cursor-not-allowed' 
        : '';
        
    $textColor = $isExpired || $isUnavailable 
        ? 'text-gray-400 dark:text-gray-500' 
        : 'text-gray-900 dark:text-white';
@endphp

<div 
    class="{{ $cardClasses }}"
    @if($onClick && !$isExpired && !$isUnavailable) onclick="{{ $onClick }}" @endif
>
    <!-- Voucher Icon/Image -->
    <div class="flex-shrink-0 w-16 h-16 mr-3">
        @if(isset($voucher['image']) && $voucher['image'])
            <img 
                src="{{ $voucher['image'] }}" 
                alt="Voucher"
                class="w-full h-full object-cover rounded-lg"
            />
        @else
            <!-- Default Voucher Icon -->
            <div class="w-full h-full bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center mx-auto mb-1">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="text-xs font-bold text-white leading-tight">
                        {{ $voucher['brand'] ?? 'GRATIS' }}<br>
                        {{ $voucher['type'] ?? 'ONGKIR' }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Voucher Details -->
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h5 class="font-medium {{ $textColor }} text-sm truncate">
                    {{ $voucher['title'] ?? 'Gratis Ongkir' }}
                </h5>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $voucher['description'] ?? 'Min. Blj Rp0' }}
                </p>
                
                <!-- Expiry Date -->
                <div class="flex items-center mt-2 text-xs">
                    <span class="text-gray-400 dark:text-gray-500">S/D: </span>
                    <span class="{{ $isExpired ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $voucher['expiry_date'] ?? '31.08.2025' }}
                    </span>
                    @if(isset($voucher['code']))
                        <span class="ml-2 px-1.5 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs rounded">
                            {{ $voucher['code'] }}
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Quantity Badge (if applicable) -->
            @if(isset($voucher['quantity']) && $voucher['quantity'] > 1)
                <div class="flex-shrink-0 ml-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full">
                        x{{ $voucher['quantity'] }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Selection Radio Button -->
    <div class="flex-shrink-0 ml-3">
        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-cyan-500 bg-cyan-500' : 'border-gray-300 dark:border-gray-600' }}">
            @if($isSelected)
                <div class="w-2 h-2 bg-white rounded-full"></div>
            @endif
        </div>
    </div>

    <!-- Status Indicators -->
    @if($isExpired)
        <div class="absolute top-2 right-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                Expired
            </span>
        </div>
    @elseif($isUnavailable)
        <div class="absolute top-2 right-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                Not Available
            </span>
        </div>
    @endif

    <!-- Warning Message -->
    @if(isset($voucher['warning_message']) && $voucher['warning_message'])
        <div class="absolute -bottom-1 left-3 right-3">
            <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800 rounded px-2 py-1">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-orange-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span class="text-xs text-orange-700 dark:text-orange-400">
                        {{ $voucher['warning_message'] }}
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>