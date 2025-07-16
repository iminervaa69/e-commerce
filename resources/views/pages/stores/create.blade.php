@extends('layouts.main')
@section('title')
Add Product
@endsection
@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <p class="block mb-2 text-m ms-2 font-medium text-gray-900 dark:text-white">Store Details</p>
        <div class="flex flex-column mt-10 sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-4">
            <x-button.button1 href="{{ route('stores.index') }}" id="btn-back" label='Back' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg"  class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>'/>
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <form class="mx-auto h-full mb-3" action="{{ route('stores.store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="flex gap-10 w-full p-4">
                <div class="w-1/2">
                    <x-input.label for="name" label="Store Name"/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store-icon lucide-store"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>'
                        id="store-name" type="text" name="name" 
                        placeholder='Store Name' required />
                    <x-input.label for="address" label="Store Address" class='mt-4'/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>' 
                        id="store-address" type="text" name="address" 
                        placeholder='Store Address' required />
                    <x-input.label for="email" label="Store Email" class='mt-4'/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>'
                        id="store-email" type="email" name="email"
                        placeholder='Store Email' required />
                    <x-input.label for="phone" label="Store Phone" class='mt-4'/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-icon lucide-phone"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>'
                        id="store-phone" type="text" name="phone"
                        placeholder='Store Phone' required />
                    <x-input.label for="dayOfWeek" label="Store Phone" class='mt-4'/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-icon lucide-phone"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>'
                        id="store-dayofweek" type="text" name="day_of_week"
                        placeholder='Store Phone' required />
                </div>

                <div class="w-1/2">
                    <x-input.label for="description" label="Store Description"/>
                    <x-input.textarea id="store-description" name="description" placeholder='Store Description'/>
                    <x-input.label label="Store Open Hour" class='mt-4'/>
                    <div class="flex gap-4 w-full ">
                        <div class="flex w-2/5">
                            <x-input.timeinput id="start-time" type="time" name="open_time" 
                                min="04:00" max="21:00" required 
                                svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>'
                                placeholder='Store Open Hour' required />
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm self-center">-</p>
                        <div class="flex w-2/5">
                            <x-input.timeinput id="end-time" type="time" name="close_time" 
                                min="04:00" max="21:00" required 
                                svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>' 
                                placeholder='Store Open Hour' required/>
                        </div>
                    </div>
                    <x-input.label for="store" label="Store Status" class='mt-4'/>
                    <x-input.toggle name="status" id="store-status" id_toggleon="btn-open" id_toggleoff="btn-close" toggleOn="Open" toggleOff="Close" toggle="open"/>
                </div>
            </div>
            <div id="save-group" class="flex flex-column mt-10 sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-4">
                <x-button.button1 label="Save" id="btn-save" color="green" type="submit"
                    svg='<svg class="w-4 h-5  mr-1.5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>'/>
            </div>
        </form>
    </div>
</div>
@endsection

@section('insert-scripts')
<script>
const updateToggleStyle = (state) => {
    const btnOpen = document.getElementById('btn-open');
    const btnClose = document.getElementById('btn-close');

    if (state === 'open') {
        console.log('style to open');

        //btnOpen.removeAttribute('disabled');
        btnOpen.className = 'w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2 border-green-800 text-white bg-green-700 hover:bg-green-800 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800';
        //btnClose.addAttribute('disabled', true);
        btnClose.className = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg text-white border-gray-800 bg-gray-700 hover:bg-gray-800 focus:ring-2 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-900';
    } else {
        console.log('style to close');

        //btnOpen.addAttribute('disabled', true);
        btnOpen.className = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2 border-gray-800 text-white bg-gray-700 hover:bg-gray-800 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800';
        // btnClose.removeAttribute('disabled');
        btnClose.className = 'w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg text-white border-red-800 bg-red-700 hover:bg-red-800 focus:ring-2 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900';
    }
};


document.addEventListener('DOMContentLoaded', function () {

    const btnOpen = document.getElementById('btn-open');
    const btnClose = document.getElementById('btn-close');
    const storeStatus = document.getElementById('store-status-hidden');

    const setToggleState = (state) => {
        storeStatus.setAttribute('toggle', state);
        storeStatus.setAttribute('value', state);
        const current = storeStatus.getAttribute('toggle');
        console.log('Toggle is now:', current);
        updateToggleStyle(state);
    };

    btnOpen.addEventListener('click', () => setToggleState('open' , console.log('open')));
    btnClose.addEventListener('click', () => setToggleState('close', console.log('close')));
});
</script>
@endsection