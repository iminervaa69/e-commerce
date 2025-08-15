@props(['id', 'name', 'type', 'svg', 'placeholder' => '', 'value' => '','required' => false, 'right_svg' => '', 'parentClass' => ''])

<div class="@twMerge('flex', $parentClass)">
    @if ($svg)
        <span
            class="inline-flex items-center px-3 text-sm text-gray-900 rounded-s-md">
            {!! $svg !!}
        </span>
    @endif

    <input 
        @if ($disabled==true)
            disabled
        @endif
        name="{{ $name }}" 
        type="{{ $type }}" 
        id="{{ $id }}"
        placeholder="{{ $placeholder }}" 
        value="{{ $value }}"
        @if ($required) required @endif
    >
</div>
