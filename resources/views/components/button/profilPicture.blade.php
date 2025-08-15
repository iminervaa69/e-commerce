@props(['id', 'type' => 'button', 'label', 'href' => '', 'data' => '', 'imageURL' => '', 'parentClass' => ''])

<button 
    class="@twMerge('flex mx-10 mr-10 ext-sm bg-gray-800 rounded-full md:mr-0 flex-shrink-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600', $parentClass)"
    type="{{ $type }}" 
    @if($href) href="{{ $href }}" @endif
    id="{{ $id }}" 
    aria-expanded="false" 
    data-dropdown-toggle="{{ $data }}">
    <img class="w-8 h-8 rounded-full" src="{{ $imageURL }}" alt="user photo">
</button>
@if ($label)
    <span class="self-center hidden sm:flex whitespace-nowrap text-gray-900 dark:text-white transition-colors duration-300 ml-3">{{ $label }}</span>
@endif