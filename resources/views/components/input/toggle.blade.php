@props(['id', 'name','id_toggleon', 'id_toggleoff', 'toggleOff', 'toggleOn', 'required' => false, 'toggle' => 'open', ])

<input type="hidden" name="{{ $name }}-hidden" value="{{ $toggle }}" id="{{ $id }}-hidden" />

@if ($toggle == 'open' || $toggle == 'available' || $toggle == '1' || $toggle == 'true')
    <div value = "{{ $toggle }}" id="{{ $id }}" class="inline-flex rounded-md shadow-xs w-full" role="group" toggle = {{ $toggle }} >
        <button id="{{ $id_toggleon }}"  type="button" class="w-full inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2  border-green-800 text-white bg-green-700 hover:bg-green-800  focus:ring-green-300">
        <svg class="w-3 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
        </svg>
        {{ $toggleOn }}
        </button>
        <button id="{{ $id_toggleoff }}"  type="button" class="w-2/10 inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg  text-white border-gray-800 bg-gray-700 hover:bg-gray-800 focus:ring-2 focus:ring-gray-300 ">
        <svg class="w-3 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
            <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
        </svg>
        {{ $toggleOff }}
        </button>
    </div>
@elseif ($toggle == 'close' || $toggle == 'unavailable' || $toggle == '0' || $toggle == 'false')
    <div value="{{ $toggle }}" id="{{ $id }}" class="inline-flex rounded-md shadow-xs w-full" role="group"  >
        <button  id="{{ $id_toggleon }}"  type="button" class="w-2/10 inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2  border-gray-800 text-white bg-gray-700 hover:bg-gray-800  focus:ring-gray-300">
        <svg class="w-3 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
        </svg>
        {{ $toggleOn }}
        </button>
        <button id="{{ $id_toggleoff }}"  type="button" class="w-full inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg  text-white border-red-800 bg-red-700 hover:bg-red-800 focus:ring-2 focus:ring-red-300 ">
        <svg class="w-3 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
            <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
        </svg>
        {{ $toggleOff }}
        </button>
    </div>
@endif
