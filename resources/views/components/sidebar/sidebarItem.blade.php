@props(['href'=>"#", 'svg', 'label'])

<li>
    <a href="{{ $href }}"
        class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group">
        {!! $svg !!}
        <span class="flex-1 ms-5 whitespace-nowrap dark:text-white">{{ $label }}</span>
    </a>
</li>
