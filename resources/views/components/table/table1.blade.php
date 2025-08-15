@props([
    'id',
    'data' => [],
    'columns_heads' => [],
    'columns_bodys' => [],
    'actions' => [],
])

<table id="{{ $id }}" class="w-full text-sm text-left rtl:text-right text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
            <th scope="col" class="p-4">
                <div class="flex items-center">
                    <input id="checkbox-all-search" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 focus:ring-2">
                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                </div>
            </th>
            @foreach ($columns_heads as $column_head)
                <th scope="col" class="px-6 py-3">{{ $column_head }}</th>
            @endforeach
            <th scope="col" class="pr-23 py-3 text-end whitespace-nowrap w-auto">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                <td class="w-4 p-4">
                    <div class="flex items-center">
                        <input id="checkbox-table-search-{{ $item->id }}" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 focus:ring-2">
                        <label for="checkbox-table-search-{{ $item->id }}" class="sr-only">checkbox</label>
                    </div>
                </td>
                @foreach ($columns_bodys as $key)
                    @if ($key == 'status')
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium {{ data_get($item, $key) == 'available'|| data_get($item, $key) == 'active' ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }} rounded-full">
                                {{ data_get($item, $key) }}
                            </span>
                        </td>
                    @elseif (in_array($key, ['image', 'image_url']))
                        <td class="px-6 py-4">
                            {!! data_get($item, $key) !!}
                        </td>
                    @else
                        <td class="px-6 py-4">
                            {{ data_get($item, $key) }}
                        </td>
                    @endif
                @endforeach
                <td class="pr-17 py-4 flex items-center justify-end space-x-2">
                    @foreach ($actions as $label => $url)
                        @if ($label === 'Delete')
                            <x-alert.deleteComfirmation 
                                title="Delete Confirmation"
                                :description="'Are you sure you want to delete ' . (property_exists($item, 'name') ? $item->name : 'this item') . '?'"
                                :action="is_callable($url) ? $url($item) : route($url, $item->id)"
                                method="DELETE"
                            />
                        @elseif ($label === 'Remove')
                            <x-alert.deleteComfirmation 
                                title="Remove Confirmation"
                                :description="'Are you sure you want to remove ' . (property_exists($item, 'name') ? $item->name : 'this item') . ' from the product?'"
                                :action="is_callable($url) ? $url($item) : route($url, $item->id)"
                                method="DELETE"
                            />
                        @else
                            <a 
                                href="{{ is_callable($url) ? $url($item) : route($url, $item->id) }}"
                                class="text-sm text-blue-600 hover:underline"
                            >
                                {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
