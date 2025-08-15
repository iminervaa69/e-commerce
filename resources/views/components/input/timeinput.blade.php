@props(['id', 'name', 'type' => 'time', 'max', 'min', 'svg', 'placeholder' => '', 'value' => '','required' => false])

<div class="flex relative w-full">
    <div class="p-4 absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none  text-gray-900 bg-gray-200  border-gray-300 rounded-r-md">
        {!! $svg !!}
    </div>
    <input name="{{ $name }}" type="{{ $type }}" id="{{ $id }}" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"  
        min="{{ isset($min) ? $min : '04:00' }}" 
        max="{{ isset($max) ? $max : '21:00' }}" 
        value="{{ $value }}" 
        @isset($required) required @endisset/>
</div>