{{-- resources/views/components/filter-section.blade.php --}}
@props([
    'title',
    'expanded' => false,
    'id' => null
])

@php
    $componentId = $id ?? 'filter-' . Str::slug($title);
@endphp

<div class="border-b border-gray-200 dark:border-gray-700 py-4">
    <button 
        type="button"
        class="flex justify-between items-center w-full text-left focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 rounded-sm"
        onclick="toggleFilter('{{ $componentId }}')"
        aria-expanded="{{ $expanded ? 'true' : 'false' }}"
        aria-controls="{{ $componentId }}-content"
    >
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wide">
            {{ $title }}
        </h3>
        <svg 
            id="{{ $componentId }}-icon" 
            class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200 {{ $expanded ? 'rotate-180' : '' }}"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div 
        id="{{ $componentId }}-content" 
        class="mt-3 space-y-2 {{ $expanded ? '' : 'hidden' }}"
    >
        {{ $slot }}
    </div>
</div>

@push('scripts')
<script>
function toggleFilter(filterId) {
    const content = document.getElementById(filterId + '-content');
    const icon = document.getElementById(filterId + '-icon');
    const button = content.previousElementSibling;
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
        button.setAttribute('aria-expanded', 'true');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
        button.setAttribute('aria-expanded', 'false');
    }
}
</script>
@endpush