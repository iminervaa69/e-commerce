@props(['svg' => '','value',
    'right_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down-icon"><path d="m6 9 6 6 6-6"/></svg>'
])

@if ($svg)
    {!! $svg !!}
@endif

<span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">
    {{ $value }}
</span>

@if ($right_svg)
    {!! $right_svg !!}
@endif

