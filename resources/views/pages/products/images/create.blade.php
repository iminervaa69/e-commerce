@extends('layouts.main')

@section('title')
Add Product Image
@endsection

@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <p class="block mb-2 text-m ms-2 font-medium text-gray-900 dark:text-white">Add Product Image</p>
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-4">
            <x-button.button1 href="{{ route('products.edit', ['product' => $product->id]) }}" id="btn-back" label='Back' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg"  class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>' />
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <form class="mx-auto h-full" action="{{ route('products.images.store', ['product' => $product->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="flex gap-10 w-full p-4">
                <div class="w-1/2">
                    <x-input.label for="images-product" label="Product"/>
                    <x-input.input 
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag"><path d="M16 10a4 4 0 0 1-8 0"/><path d="M3.103 6.034h17.794"/><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"/></svg>'
                        id="images-product" 
                        type="text"
                        right_svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>'
                        right_svg_dropdown_id="product-selector"
                        value="{{ $product->id ? $product->name : 'N/A' }}" 
                        placeholder="Select Product" 
                        required 
                        disabled=true
                    />
                    <x-form.dropdownSelect
                        id="product-selector"
                        search_id="search-product"
                        search_placeholder="Search product..."
                        name="product_id"
                        inputValue="{{ $product->id }}"
                        :items="$products"
                        :value="old('product_id', $defaultStoreId ?? '')"
                        parentClass="mt-2"
                    />
                    <x-input.label for="images-variant" label="Related Variant" class="mt-4"/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-puzzle-icon lucide-puzzle"><path d="M15.39 4.39a1 1 0 0 0 1.68-.474 2.5 2.5 0 1 1 3.014 3.015 1 1 0 0 0-.474 1.68l1.683 1.682a2.414 2.414 0 0 1 0 3.414L19.61 15.39a1 1 0 0 1-1.68-.474 2.5 2.5 0 1 0-3.014 3.015 1 1 0 0 1 .474 1.68l-1.683 1.682a2.414 2.414 0 0 1-3.414 0L8.61 19.61a1 1 0 0 0-1.68.474 2.5 2.5 0 1 1-3.014-3.015 1 1 0 0 0 .474-1.68l-1.683-1.682a2.414 2.414 0 0 1 0-3.414L4.39 8.61a1 1 0 0 1 1.68.474 2.5 2.5 0 1 0 3.014-3.015 1 1 0 0 1-.474-1.68l1.683-1.682a2.414 2.414 0 0 1 3.414 0z"/></svg>    '
                        id="images-variant" 
                        type="text"
                        right_svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down-icon lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>'
                        right_svg_dropdown_id="variant-selector"
                        placeholder='Related Variant'
                        required 
                        disabled=true
                    />
                    <x-form.dropdownSelect
                        id="variant-selector"
                        search_id="search-variant"
                        search_placeholder="Search variant..."
                        name="variant_id"
                        :items="$productVariants"
                        :value="old('variant_id', $defaultStoreId ?? '')"
                        parentClass="mt-2"
                    />
                    <x-input.label for="images-variant" label="Primary" class="mt-4"/>
                    <x-input.toggle name="primary" id="image-primary" id_toggleon="btn-yes" id_toggleoff="btn-no" toggleOn="Yes" toggleOff="No" toggle="{{ 1}}"/>
                </div>
                <div class="w-1/2">
                    <x-input.label for="image" label="Upload Image"/>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-white dark:bg-gray-700 dark:border-gray-600" />
                    @error('image')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>  
            </div>
            <div id="save-group"
                class="flex flex-column mt-4 sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pr-2 pb-2">
                <x-button.button1 label="Save" id="btn-save" color="green" type="submit"
                    svg='<svg class="w-4 h-5  mr-1.5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>' />
            </div>
        </form>
    </div>
</div>
@endsection


@section('insert-scripts')
<script>
    const updateToggleStyle = (state) => {
        const btnYes = document.getElementById('btn-yes');
        const btnNo = document.getElementById('btn-no');

        if (state === 'yes') {
            btnYes.className = 'w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2 border-green-800 text-white bg-green-700 hover:bg-green-800 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800';
            btnNo.className = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg text-white border-gray-800 bg-gray-700 hover:bg-gray-800 focus:ring-2 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-900';
        } else {
            btnYes.className = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2 border-gray-800 text-white bg-gray-700 hover:bg-gray-800 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800';
            btnNo.className = 'w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg text-white border-red-800 bg-red-700 hover:bg-red-800 focus:ring-2 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900';
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const btnYes = document.getElementById('btn-yes');
        const btnNo = document.getElementById('btn-no');
        const imagePrimary = document.getElementById('image-primary-hidden');

        const setToggleState = (state) => {
            imagePrimary.setAttribute('toggle', state);
            imagePrimary.setAttribute('value', state);
            updateToggleStyle(state);
        };

        btnYes.addEventListener('click', () => setToggleState('yes'));
        btnNo.addEventListener('click', () => setToggleState('no'));

        document.querySelectorAll('#product-selector li').forEach(item => {
            item.addEventListener('click', function () {
                const productName = this.dataset.name;
                const productId = this.dataset.id;

                document.getElementById('images-product').value = productName;
                document.getElementById('hidden-product-selector').value = productId;
            });
        });

        document.querySelectorAll('#variant-selector li').forEach(item => {
            item.addEventListener('click', function () {
                const variantName = this.dataset.name;
                const variantId = this.dataset.id;

                document.getElementById('images-variant').value = variantName;
                document.getElementById('hidden-variant-selector').value = variantId;
            });
        });

        document.getElementById('search-product').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const items = document.querySelectorAll('#product-selector li');

            let anyVisible = false;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = '';
                    anyVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });

            const dropdown = document.getElementById('product-selector');
            if (filter && anyVisible) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        });

        document.getElementById('search-variant').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const items = document.querySelectorAll('#variant-selector li');

            let anyVisible = false;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filter)) {
                    item.style.display = '';
                    anyVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });

            const dropdown = document.getElementById('variant-selector');
            if (filter && anyVisible) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        });

        document.getElementById('images-product').value.addEventListener('change', function () {
            const productId = this.value;
            console.log();
            fetch(`/api/product/${productId}/variants`)
                .then(res => res.json())
                .then(variants => {
                    const variantSelect = document.getElementById('variant-selector');
                    const searchInput = document.getElementById('search-variant');

                    variantSelect.innerHTML = '';
                    searchInput.value = '';

                    variantSelect.classList.remove(':items');
                    variantSelect.classList.add(':items', $variants);

                })
                .catch(err => console.error('Failed to load variants', err));
        });
    });
</script>
@endsection
