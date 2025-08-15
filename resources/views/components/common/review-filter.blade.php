 {{-- resources/views/components/review-filter.blade.php --}}
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 shadow-sm transition-colors duration-200">
    <div class="flex justify-between items-center mb-6">
        <p class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wide">Filter Ulasan</p>
        
        @if($hasActiveFilters)
            <button type="button" 
                    onclick="clearAllFilters()"
                    class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-bold">
                Clear
            </button>
        @endif
    </div>
    
    <form method="GET" action="{{ request()->url() }}" id="review-filter-form">
        {{-- Preserve other query parameters --}}
        @foreach(request()->except(['rating', 'topics', 'media', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        {{-- Media Filter --}}
        {{-- <x-common.filter-section title="Media" :expanded="in_array('photo_video', $selectedFilters['media'])">
            <x-input.checkbox-group 
                name="media"
                :options="$mediaOptions"
                :selected="$selectedFilters['media']"
            />
        </x-common.filter-section> --}}

        {{-- Rating Filter --}}
        <x-common.filter-section title="Rating" :expanded="!empty($selectedFilters['rating'])">
            <x-input.checkbox-group
                name="rating"
                :options="$ratingOptions"
                :selected="$selectedFilters['rating']"
                :show-stars="true"
            />
        </x-common.filter-section>

        {{-- Topics Filter --}}
        {{-- @if($topicOptions->count() > 0)
            <x-common.filter-section title="Topik Ulasan" :expanded="!empty($selectedFilters['topics'])">
                <x-input.checkbox-group
                    name="topics"
                    :options="$topicOptions->toArray()"
                    :selected="$selectedFilters['topics']"
                />
            </x-common.filter-section>
        @endif --}}
    </form>
</div>

@push('scripts')
<script>
function clearAllFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('#review-filter-form input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Submit form to clear filters
    document.getElementById('review-filter-form').submit();
}

function resetFilters() {
    // Uncheck all checkboxes but don't auto-submit
    document.querySelectorAll('#review-filter-form input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Auto-submit on change (optional)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('review-filter-form');
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Optional: Auto-submit form on change
            // form.submit();
        });
    });
});
</script>
@endpush