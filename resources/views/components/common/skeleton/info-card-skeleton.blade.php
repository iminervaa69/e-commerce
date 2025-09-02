{{-- Product Info Card Skeleton --}}
<div class="space-y-6" role="status">
    {{-- Title and Subtitle Skeleton --}}
    <div>
        <div class="h-8 bg-gray-200 rounded-full dark:bg-gray-700 w-3/4 mb-2 animate-pulse"></div>
        <div class="h-5 bg-gray-200 rounded-full dark:bg-gray-700 w-1/2 mb-2 animate-pulse"></div>
        
        {{-- Rating Skeleton --}}
        <div class="flex items-center space-x-2">
            <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-24 animate-pulse"></div>
            <div class="flex items-center space-x-1">
                @for($i = 1; $i <= 5; $i++)
                    <div class="w-4 h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                @endfor
                <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-20 ml-1 animate-pulse"></div>
            </div>
        </div>
    </div>

    {{-- Price Skeleton --}}
    <div class="flex items-center space-x-3">
        <div class="h-9 bg-gray-200 rounded-full dark:bg-gray-700 w-32 animate-pulse"></div>
        <div class="h-6 bg-gray-200 rounded-full dark:bg-gray-700 w-24 animate-pulse"></div>
        <div class="h-6 bg-gray-200 rounded-full dark:bg-gray-700 w-20 animate-pulse"></div>
    </div>

    {{-- Variants Skeleton --}}
    <div class="space-y-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
        <div class="h-6 bg-gray-200 rounded-full dark:bg-gray-700 w-32 animate-pulse"></div>
        
        {{-- Color Variant Skeleton --}}
        <div class="space-y-3">
            <div class="flex items-center space-x-2">
                <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-16 animate-pulse"></div>
                <div class="w-2 h-4 bg-red-200 rounded animate-pulse"></div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @for($i = 1; $i <= 4; $i++)
                <div class="flex flex-col items-center p-2 border-2 border-gray-200 dark:border-gray-600 rounded-lg animate-pulse">
                    <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white shadow-sm mb-1"></div>
                    <div class="h-3 bg-gray-200 rounded dark:bg-gray-700 w-12"></div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Size Variant Skeleton --}}
        <div class="space-y-3">
            <div class="flex items-center space-x-2">
                <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-12 animate-pulse"></div>
                <div class="w-2 h-4 bg-red-200 rounded animate-pulse"></div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @for($i = 1; $i <= 5; $i++)
                <div class="flex flex-col items-center px-4 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-lg min-w-[60px] animate-pulse">
                    <div class="h-4 bg-gray-200 rounded dark:bg-gray-700 w-8"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>
    
    {{-- Product Details Skeleton --}}
    <div class="py-3 space-y-3 border-t border-b border-gray-400 dark:border-gray-700">
        @for($i = 1; $i <= 4; $i++)
        <div class="flex justify-between animate-pulse">
            <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-24"></div>
            <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-32"></div>
        </div>
        @endfor
        
        {{-- Tags Skeleton --}}
        <div class="flex justify-between animate-pulse">
            <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-16"></div>
            <div class="flex flex-wrap gap-1">
                @for($i = 1; $i <= 3; $i++)
                <div class="h-6 bg-gray-200 rounded dark:bg-gray-700 w-16"></div>
                @endfor
            </div>
        </div>
    </div>

    <span class="sr-only">Loading product information...</span>
</div>