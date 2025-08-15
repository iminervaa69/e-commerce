<div class="review-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm transition-colors duration-200">
    <div class="flex flex-col lg:flex-row lg:items-start gap-6">
        <div class="flex-shrink-0">
            <div class="flex items-center gap-2 mb-2">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-400 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                
                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($averageRating, 1) }}
                </span>
                <span class="text-gray-500 dark:text-gray-400">
                    / {{ $maxRating }}
                </span>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                {{ $satisfactionText }}
            </p>

            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $totalReviews }} rating • {{ $totalComments }} ulasan
            </p>
        </div>

        <div class="flex-1">
            <div class="space-y-2">
                @foreach([5, 4, 3, 2, 1] as $star)
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 w-8">
                            <svg class="w-3 h-3 text-yellow-400 fill-current flex-shrink-0" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $star }}</span>
                        </div>

                        <div class="flex-1 relative">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div 
                                    class="bg-green-500 dark:bg-green-400 h-2 rounded-full transition-all duration-300 ease-out"
                                    style="width: {{ $getPercentage($ratings[$star] ?? 0) }}%"
                                ></div>
                            </div>
                        </div>

                        <div class="w-8 text-right">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                ({{ $ratings[$star] ?? 0 }})
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.review-card {
    transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

@media (max-width: 1023px) {
    .review-card .flex-col {
        gap: 1rem;
    }
}

.review-card:hover {
    @apply shadow-md;
}
</style>