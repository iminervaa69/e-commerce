@props([
    'tabs' => [],
    'parentClass' => '',
])

<div class="@twMerge('mb-4 border-b border-gray-200', $parentClass)">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="main-tab" data-tabs-toggle="#main-tab-content" role="tablist">
        @foreach ($tabs as $index => $tab)
            <li class="me-2" role="presentation">
                <button
                    class="{{ $index === 0 
                        ? 'inline-block p-4 border-b-2 rounded-t-lg text-blue-600 border-blue-600 font-extrabold'
                        : 'inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold' }}"
                    id="{{ $tab['id'] }}-tab"
                    data-tabs-target="#{{ $tab['id'] }}"
                    type="button"
                    role="tab"
                    aria-controls="{{ $tab['id'] }}"
                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    {{ $tab['label'] }}
                </button>
            </li>
        @endforeach
    </ul>
</div>