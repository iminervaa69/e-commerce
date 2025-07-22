@extends('layouts.main')
@section('title')
Edit Product - {{ $product->name }}
@endsection

@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between">
        <p class="block mb-2 text-m ms-2 font-medium text-gray-900 dark:text-white">Category Details</p>
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-4">
            <x-button.button1 href="{{ route('products.index') }}" id="btn-back" label='Back' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg"  class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>'/>
            <x-button.button1 id="btn-edit" label='Edit' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>'/>
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <form class="mx-auto h-full" action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex gap-10 w-full p-4">
                <div class="w-1/2">
                    <x-input.label for="product-name" label="Product Name"/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag"><path d="M16 10a4 4 0 0 1-8 0"/><path d="M3.103 6.034h17.794"/><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"/></svg>'
                        id="product-name" type="text" name="name"
                        value='{{ $product->name }}' placeholder='Product Name' required />
                    <x-input.label for="product-description" label="Product Description" class="mt-4"/>
                    <x-input.textarea svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store-icon lucide-store"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>'
                        id="product-description" name="description"
                        value='{{ $product->description }}' placeholder='Product Description' required />
                    <x-input.label for="product-store" label="Store" class="mt-4"/>
                    <x-input.input svg='<svg xmlns="<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store-icon lucide-store"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>'
                        id="product-store" type="text" name="store"
                        right_svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down-icon lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>'
                        right_svg_dropdown_id="store-selector"
                        value="{{ $product->store_id ? $product->store->name : 'N/A' }}" placeholder='Store' required />
                    <x-form.dropdownSelect
                        id="store-selector"
                        search_id="search-store"
                        search_placeholder="Search store..."
                        name="store_id"
                        :items="$stores"
                        :value="old('store_id', $defaultStoreId ?? '')"
                        parentClass="mt-2"
                    />
                </div>
                <div class="w-1/2">
                    {{-- <x-input.label  label="Price Range"/>
                    <div class="flex gap-4 w-full ">
                        <div class="flex w-1/2">
                            <x-input.input id="price-min" type="text" parentClass="w-full"
                                svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>'
                                placeholder='Minimum Price' required />
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm self-center">-</p>
                        <div class="flex w-1/2">
                            <x-input.input id="price-max" type="text" parentClass="w-full"
                                svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>'
                                placeholder='Maximum Price' required/>
                        </div>
                    </div> --}}
                    <x-input.label  label="Product Category"/>
                    <x-input.input svg='<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>'
                            id="category-search" type="text" placeholder='Category search'
                            parentClass='w-100 mb-2'/>
                    <x-table.table1
                        parentClass="mt-2"
                        id="category-table"
                        :data="$productCategories"
                        :columns_heads="['Name', 'id']"
                        :columns_bodys="['name', 'id']"
                    />
                </div>
            </div>
            <div id="save-group" class="flex mt-4 flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pr-2 pb-2">
                <x-button.button1 label="Cancel" id="btn-cancel" color="red"
                    svg='<svg class="w-4 h-5 mr-1.5 text-red-500 group-hover:text-white dark:text-red-500 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>'/>
                <x-button.button1 label="Save" id="btn-save" color="green" type="submit"
                    svg='<svg class="w-4 h-5  mr-1.5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>'/>
            </div>
        </form>
    </div>
</div>

<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-5">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <x-common.tab_header
            :tabs="[
                ['id' => 'variants', 'label' => 'Variants'],
                ['id' => 'images', 'label' => 'Images']
            ]"
        />
        <div id="main-tab-content">
            <div role="tabpanel" class="rounded-lg bg-gray-50 dark:bg-gray-800"
                id="variants" aria-labelledby="variants-tab">
                <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between mb-2">
                    <x-input.input svg='<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>'
                        id="variant-search" type="text"
                        placeholder="Variant search" parentClass="w-100"/>
                    <x-button.button1 id="btn-add" label='Add' color='green'
                        type="button"  href="{{ route('products.create') }}" onclick="window.location.href='{{ route('stores.create') }}'"
                        svg='<svg xmlns="http://www.w3.org/2000/svg"  width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="pr-1 lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>'/>
                </div>
                <x-table.table1
                    parentClass="mt-2"
                    id="image-table"
                    :data="$productVariants"
                    :columns_heads="['Name', 'Description', 'Price', 'Stock', 'Status']"
                    :columns_bodys="['name', 'description', 'price', 'stock', 'status']"
                    :actions="[
                        'Edit' => fn($productVariant) => route('products.variants.edit', ['product' => $productVariant->product_id, 'variant' => $productVariant->id]),
                        'Remove' => fn($productVariant) => route('products.variants.destroy', ['product' => $productVariant->product_id, 'variant' => $productVariant->id])
                    ]"
                />
            </div>
            <div role="tabpanel" class="rounded-lg bg-gray-50 dark:bg-gray-800"
                id="images" aria-labelledby="images-tab">
                <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between mb-2">
                    <x-input.input svg='<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>'
                        id="variant-search" type="text"
                        placeholder="Variant search" parentClass="w-100"/>
                    <x-button.button1
                        id="btn-add"
                        label="Add"
                        color="green"
                        type="button"
                        href="{{ route('products.images.create', ['product' => $product->id]) }}"
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="pr-1 lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>' />
                    </div>
                <x-table.table1
                    id="variant-image-table"
                    :data="$productImages"
                    :columns_heads="['Image', 'URL', 'Primary', 'Related Variant']"
                    :columns_bodys="['image', 'image_url', 'is_primary', 'product_variant_id']"
                    :actions="[
                        'Edit' => fn($img) => route('products.images.edit', ['product' => $img->product_id, 'image' => $img->id]),
                        'Remove' => fn($img) => route('products.images.destroy', ['product' => $img->product_id, 'image' => $img->id])
                    ]"
                />
            </div>
        </div>
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
        const productName = document.getElementById('product-name');
        const productDescription = document.getElementById('product-description');
        const productStore = document.getElementById('product-store');
        const categrySearch = document.getElementById('category-search');
        const categoryTable = document.getElementById('category-table');
        const saveGroup = document.getElementById('save-group');
        const inputs = categoryTable.querySelectorAll('input, select, textarea, button');

        if (!editable) {
            if (oldValue) {
                productName.value = oldValue.name;
                productDescription.value = oldValue.description;
                productStore.value = oldValue.store_id;
            }

            productName.setAttribute('disabled', true);
            productDescription.setAttribute('disabled', true);
            productStore.setAttribute('disabled', true);
            categrySearch.setAttribute('disabled', true);
            categoryTable.setAttribute('disabled', true);
            btnEdit.removeAttribute('disabled');
            saveGroup.classList.add('hidden');

            inputs.forEach(input => {
                input.disabled = true;
            });
        } else {
            oldValue = {
                name: productName.value,
                description: productDescription.value,

            };

            productName.removeAttribute('disabled');
            productDescription.removeAttribute('disabled');
            productStore.removeAttribute('disabled');
            categrySearch.removeAttribute('disabled');
            categoryTable.removeAttribute('disabled');
            btnEdit.setAttribute('disabled', true);
            saveGroup.classList.remove('hidden');

            inputs.forEach(input => {
                input.disabled = false;
            });
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

document.addEventListener('DOMContentLoaded', function () {
    initEditable();
});
</script>
@endsection
