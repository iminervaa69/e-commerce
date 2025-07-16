@props(['id','items' => []])

<ul id="{{ $id }}" class="hidden py-2 space-y-2">
    @foreach ($items as $item)
        <li>
            <a href="{!! $item['href'] ?? '#' !!}"
                class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                
                @if (!empty($item['svg']))
                    {!! $item['svg'] !!}
                @endif

                <span class="ml-2">{{ $item['value'] ?? '' }}</span>
            </a>
        </li>
    @endforeach
</ul>
