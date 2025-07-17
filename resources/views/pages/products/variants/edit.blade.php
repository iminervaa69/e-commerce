@extends('layouts.main')
@section('title')
Edit Variant - {{ $variant->name }}
@endsection

@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between">
        <p class="block mb-2 text-m ms-2 font-medium text-gray-900 dark:text-white">Product Variant Details</p>
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-4">
            <x-button.button1 href="{{ route('products.edit', $variant->product_id) }}" id="btn-back" label='Back' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg"  class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>'/>
            <x-button.button1 id="btn-edit" label='Edit' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>'/>
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <form class="mx-auto h-full" action="{{ route('products.variants.update', [$variant->product_id, $variant->id])}}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex gap-10 w-full p-4">
                <div class="w-1/2">
                    <x-input.label for="variant-name" label="Variant Name"/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag"><path d="M16 10a4 4 0 0 1-8 0"/><path d="M3.103 6.034h17.794"/><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"/></svg>'
                        id="variant-name" type="text" name="name" 
                        value='{{ $variant->name }}' placeholder='Variant Name' required />
                    <x-input.label for="variant-description" label="Variant Description" class="mt-4"/>
                    <x-input.textarea svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store-icon lucide-store"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>'
                        id="variant-description" name="description" 
                        value='{{ $variant->description }}' placeholder='Variant Description' required />
                </div>
                <div class="w-1/2">
                    <x-input.label  label="Price"/>
                    <x-input.input id="variant-price" type="number" parentClass="w-full"
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-banknote-icon lucide-banknote"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>'
                        value='{{ $variant->price }}' placeholder='Variant Price' required />
                    <x-input.label  label="Stock" class="mt-4"/>
                    <x-input.input id="variant-stock" type="number" parentClass="w-full"
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package-icon lucide-package"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><polyline points="3.29 7 12 12 20.71 7"/><path d="m7.5 4.27 9 5.15"/></svg>'
                        value='{{ $variant->stock }}' placeholder='Variant Stock' required />
                    <x-input.label for="store" label="Store Status" class='mt-4'/>
                    <x-input.toggle name="status" id="variant-status" id_toggleon="btn-available" id_toggleoff="btn-unavailable" toggleOn="Available" toggleOff="Unavailable" toggle="{{ $variant->status}}"/>
                    </div>
            </div>
            <div id="save-group" class="flex mt-4 flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end">
                <x-button.button1 label="Cancel" id="btn-cancel" color="red"
                    svg='<svg class="w-4 h-5 mr-1.5 text-red-500 group-hover:text-white dark:text-red-500 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>'/>
                <x-button.button1 label="Save" id="btn-save" color="green" type="submit"
                    svg='<svg class="w-4 h-5  mr-1.5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>'/>
            </div>
        </form>
    </div>
</div>
@endsection

@section('insert-scripts')
<script>
let editable = false;
let oldValue = null;
const initEditable = () => {
    const btnEdit = document.getElementById('btn-edit');
    const btnCancel = document.getElementById('btn-cancel');

    const checkInput = () => {
        const variantName = document.getElementById('variant-name');
        const variantDescription = document.getElementById('variant-description');
        const variantPrice = document.getElementById('variant-price');
        const variantStock = document.getElementById('variant-stock');
        const variantStatus = document.getElementById('variant-status');
        const saveGroup = document.getElementById('save-group');
        const btnAvailable = document.getElementById('btn-available');
        const btnUnavailable = document.getElementById('btn-unavailable');

        if (!editable) {
            if (oldValue) {
                variantName.value = oldValue.name;
                variantDescription.value = oldValue.description;
                variantPrice.value = oldValue.price;
                variantStock.value = oldValue.stock;
                variantStatus.setAttribute('toggle', oldValue.status);
                updateToggleStyle(oldValue.status);
            }

            variantName.setAttribute('disabled', true);
            variantDescription.setAttribute('disabled', true);
            variantPrice.setAttribute('disabled', true);
            variantStock.setAttribute('disabled', true);
            variantStatus.setAttribute('disabled', true);
            btnEdit.removeAttribute('disabled');
            saveGroup.classList.add('hidden');

            btnAvailable.setAttribute('disabled', true);
            btnUnavailable.setAttribute('disabled', true);
        } else {
            oldValue = {
                name: variantName.value,
                description: variantDescription.value,
                price: variantPrice.value,
                stock: variantStock.value,
                status: variantStatus.getAttribute('toggle'),
            };

            variantName.removeAttribute('disabled');
            variantDescription.removeAttribute('disabled');
            variantPrice.removeAttribute('disabled');
            variantStock.removeAttribute('disabled');
            variantStatus.removeAttribute('disabled');
            btnEdit.setAttribute('disabled', true);
            saveGroup.classList.remove('hidden');

            btnAvailable.removeAttribute('disabled');
            btnUnavailable.removeAttribute('disabled');
        }
    };

    btnEdit.addEventListener('click', () => {
        console.log('edit');
        editable = true;
        checkInput();
    });

    btnCancel.addEventListener('click', () => {
        editable = false;
        checkInput();
    });

    checkInput();
};

const updateToggleStyle = (state) => {
    const btnAvailable = document.getElementById('btn-available');
    const btnUnavailable = document.getElementById('btn-unavailable');

    if (state === 'available') {
        console.log('style to available');

        btnAvailable.className = 'w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2 border-green-800 text-white bg-green-700 hover:bg-green-800 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800';

        btnUnavailable.className = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg text-white border-gray-800 bg-gray-700 hover:bg-gray-800 focus:ring-2 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-900';
    } else {
        console.log('style to unavailable');

        btnAvailable.className = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2 border-gray-800 text-white bg-gray-700 hover:bg-gray-800 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800';

        btnUnavailable.className = 'w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg text-white border-red-800 bg-red-700 hover:bg-red-800 focus:ring-2 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900';
    }
};

document.addEventListener('DOMContentLoaded', function () {
    initEditable();

    const btnAvailable = document.getElementById('btn-available');
    const btnUnavailable = document.getElementById('btn-unavailable');
    const storeStatus = document.getElementById('variant-status');
    console.log(storeStatus.getAttribute('toggle'));

    const setToggleState = (state) => {
        storeStatus.setAttribute('toggle', state);
        storeStatus.setAttribute('value', state);
        const current = storeStatus.getAttribute('toggle');
        console.log('Toggle is now:', current);
        updateToggleStyle(state);
    };

    btnAvailable.addEventListener('click', () => {
        console.log('available');
        setToggleState('available');
    });

    btnUnavailable.addEventListener('click', () => {
        console.log('unavailable');
        setToggleState('unavailable');
    });
});
</script>
@endsection