@props([
    'id',
    'search_id',
    'search_placeholder',
    'name',
    'type' => 'radio',
    'placeholder' => '',
    'inputValue' => '1',
    'required' => false,
    'parentClass' => '',
    'items' => []
])

<div id="{{ $id }}"
    class="z-10 hidden bg-white rounded-lg shadow-sm w-2/5 dark:bg-gray-700 {{ $parentClass }}">
    <div class="p-3">
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="text" id="{{ $search_id }}"
                class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="{{ $search_placeholder }}">
            <input type="hidden" name="{{ $name }}" id="hidden-{{ $id }}" value="{{ $inputValue }}">
        </div>
    </div>

    <ul class="max-h-40 h-full px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200 space-y-1">
        @foreach ($items as $item)
            <li 
                class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
            >
                {{ $item->name }}
            </li>
        @endforeach
    </ul>
</div>
