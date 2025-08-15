@props([
    'id' => null,
    'name' => 'search',
    'placeholder' => 'Search...',
    'value' => null,
    'required' => false,
    'containerClass' => '',
    'inputClass' => '',
])


<div class="@twMerge('relative', $containerClass)">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>
    </div>
    
    <input 
        type="search" 
        id="{{ $id }}"
        name="{{ $name ?? 'search' }}"
        class="@twMerge('block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500', $inputClass)" 
        placeholder="{{ $placeholder ?? 'Search...' }}"
        value="{{ $value ?? old($name ?? 'search') }}"
        @if($required ?? false) required @endif
        {{ $attributes->only(['x-model', 'x-on:input', 'x-on:keydown', 'wire:model', 'wire:keydown', '@input', '@keydown', 'autofocus', 'disabled', 'readonly']) }}
    >
</div>