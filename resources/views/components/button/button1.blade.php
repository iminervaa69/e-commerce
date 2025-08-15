@props([ 'type' => 'button', 'label', 'svg', 'id', 'color' => 'gray', 'href' => '', 'class' => '' ])

@if ($href)
    <a href="{{ $href }}" id="{{ $id }}"
        class="inline-flex items-center text-{{ $color }}-900 bg-white border border-{{ $color }}-300 focus:outline-none hover:bg-{{ $color }}-100 focus:ring-4 focus:ring-{{ $color }}-100 font-medium rounded-lg text-sm px-3.5 pr-5 py-2.5 me-2 mb-2 $color }}-800 $color }}-600 $color }}-700 $color }}-600 $color }}-700">
        @isset($svg) {!! $svg !!} @endisset
        {{ $label }}
    </a>
@else
    <button id="{{ $id }}" type="{{ $type }}"
        class="inline-flex items-center text-{{ $color }}-900 bg-white border border-{{ $color }}-300 focus:outline-none hover:bg-{{ $color }}-100 focus:ring-4 focus:ring-{{ $color }}-100 font-medium rounded-lg text-sm px-3.5 pr-5 py-2.5 me-2 mb-2 $color }}-800 $color }}-600 $color }}-700 $color }}-600 $color }}-700">
        @isset($svg) {!! $svg !!} @endisset
        {{ $label }}
    </button>
@endif
