@props([
    'id',
    'name' => '',
    'type' => 'text',
    'svg' => '',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'right_svg' => '',
    'right_svg_dropdown_id' => '',
    'right_svg_dropdown_placement' => 'bottom-end',
    'parentClass' => '',
    'inputClass' => '',
    'disabled' => false
])

<div class="@twMerge('flex', $parentClass)">
    @if ($svg)
        <span
            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
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
        class="@twMerge('rounded-none ' . ($right_svg ? '' : 'rounded-e-lg') . ' bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', $inputClass)"
    >

    @if ($right_svg)
        <span
            @if ($right_svg_dropdown_id)
                data-dropdown-toggle="{{ $right_svg_dropdown_id }}"
                data-dropdown-placement="{{ $right_svg_dropdown_placement }}"
            @endif
            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600 cursor-pointer"
        >
            {!! $right_svg !!}
        </span>
    @endif
</div>
