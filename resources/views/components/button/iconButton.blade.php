@props(['type' => 'button', 'label', 'svg', 'id', 'color' => 'gray', 'href' => '', 'class' => '', 'data' => '', 'parentClass' => ''])

<button 
    id="{{ $id }}" 
    type="{{ $type }}" 
    class="@twMerge('p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600', $parentClass)"
    @if($href) href="{{ $href }}" @endif 
    @if($data) data-dropdown-toggle="{{ $data }}" @endif
>
    <span class="sr-only">{{ $label }}</span>
    {!! $svg !!}
</button>