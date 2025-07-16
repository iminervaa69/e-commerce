@extends('layouts.main')
@section('title')
Edit Variant
@endsection
@section('content')
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
          <p for="product-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Variant Details</p>
          <button type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Light</button>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <form class="mx-auto h-full mb-15" re>
                <div class="flex gap-10 w-full p-4">
                    <div class="w-1/2">
                        <label for="variant-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Variant
                            Name</label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                                </svg>
                            </span>
                            <input disabled type="text" id="variant-name"
                                class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Variant Name" value="{{ $variant->name }}" required>
                        </div>

                        <label for="variant-description"
                            class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white">Variant
                            Description</label>
                        <textarea disabled id="variant-description" rows="4"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Variant Description" required>{{ $variant->description }}</textarea>
                        <label for="variant-price"
                            class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white">Variant Price</label>
                        <div class="flex w-full">
                          <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM7.346 5.294a.75.75 0 0 0-1.192.912L9.056 10H6.75a.75.75 0 0 0 0 1.5h2.5v1h-2.5a.75.75 0 0 0 0 1.5h2.5v1.25a.75.75 0 0 0 1.5 0V14h2.5a.75.75 0 1 0 0-1.5h-2.5v-1h2.5a.75.75 0 1 0 0-1.5h-2.306l2.902-3.794a.75.75 0 1 0-1.192-.912L10 8.765l-2.654-3.47Z" clip-rule="evenodd" />
                            </svg>
                          </span>
                          <input disabled type="text" id="variant-price"
                              class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                              placeholder="0000">
                        </div>

                        <label for="variant-stock"
                            class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white">Variant Stock</label>
                        <div class="flex w-full">
                          <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                              viewBox="0 0 20 20">
                              <path d="M10.362 1.093a.75.75 0 0 0-.724 0L2.523 5.018 10 9.143l7.477-4.125-7.115-3.925ZM18 6.443l-7.25 4v8.25l6.862-3.786A.75.75 0 0 0 18 14.25V6.443ZM9.25 18.693v-8.25l-7.25-4v7.807a.75.75 0 0 0 .388.657l6.862 3.786Z" />
                            </svg>
                          </span>
                          <input disabled type="text" id="variant-stock"
                              class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                              placeholder="0000">
                        </div>

                        <label for="variant-status"
                            class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white">Variant Status</label>
                        <div class="inline-flex rounded-md shadow-xs w-full" role="group" >
                          <button type="button" class="w-6/7 inline-flex items-center px-4 py-2 text-sm font-medium rounded-s-lg focus:ring-2  border-green-800 text-white bg-green-700 hover:bg-green-800  focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            <svg class="w-3 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                            </svg>
                            Avilable
                          </button>
                          <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-e-lg  text-white border-red-800 bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300  dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"">
                            <svg class="w-3 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                              <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                            </svg>
                            Unavilable
                          </button>
                          {{-- <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                            <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                              <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                            </svg>
                            Unavilable
                          </button> --}}
                        </div>
                    </div>

                    <div class="w-1/2">
                      <label for="variant-product"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Variant Product</label>
                      <div class="flex">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M2.879 7.121A3 3 0 0 0 7.5 6.66a2.997 2.997 0 0 0 2.5 1.34 2.997 2.997 0 0 0 2.5-1.34 3 3 0 1 0 4.622-3.78l-.293-.293A2 2 0 0 0 15.415 2H4.585a2 2 0 0 0-1.414.586l-.292.292a3 3 0 0 0 0 4.243ZM3 9.032a4.507 4.507 0 0 0 4.5-.29A4.48 4.48 0 0 0 10 9.5a4.48 4.48 0 0 0 2.5-.758 4.507 4.507 0 0 0 4.5.29V16.5h.25a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1-.75-.75v-3.5a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 0-.75.75v3.5a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1 0-1.5H3V9.032Z" />
                            </svg>
                        </span>
                        <input type="text" id="variant-product"
                            class=" bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block w-full text-sm p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Product Store" value="{{ $variant->product->name }}"
                            required disabled>
                        <button id ="variantProductBtn" disabled data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom-end"
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-r-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600"
                            type="button">
                            <svg class="w-2.5 h-2.5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" s roke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div id="dropdownSearch"
                          class="z-10 hidden bg-white rounded-lg shadow-sm w-2/5 dark:bg-gray-700">
                          <div class="p-3">
                              <div class="relative">
                                  <div
                                      class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                      <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                          xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                      </svg>
                                  </div>
                                  <input type="text" id="input-group-search"
                                      class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                      placeholder="Search products" aria-label="Search products">
                              </div>
                          </div>
                          <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                              aria-labelledby="dropdownSearchButton">
                              @foreach ($products as $product)
                                  <li>
                                      <div
                                          class="flex items-center ps-2 rounded-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                                          <input id="checkbox-item-{{ $product->id }}" type="radio" name="product_id"
                                              value="{{ $product->id }}"
                                              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                              {{ $product->product_id == $product->id ? 'checked' : '' }}>
                                          <label for="checkbox-item-{{ $product->id }}"
                                              class="w-full py-2 ms-2 text-sm font-medium text-gray-900 rounded-sm dark:text-gray-300">
                                              {{ $product->name }}
                                          </label>
                                      </div>
                                  </li>
                              @endforeach
                          </ul>
                        </div>
                    </div>
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
        const checkInput = () => {
            const variantName = document.getElementById('variant-name');
            const variantDescription = document.getElementById('variant-description');
            const variantPrice = document.getElementById('variant-price');
            const variantStatus = document.getElementById('variant-status');
            const variantStock = document.getElementById('variant-stock');
            const variantProduct = document.getElementById('variant-product');

            if (!editable) {
                if(oldValue) {
                    productName.value = oldValue.name;
                    productDescription.value = oldValue.description;
                    productStore.value = oldValue.store;
                    productPriceButton.value = oldValue.price;
                }

                variantName.setAttribute('disabled', true);
                variantDescription.setAttribute('disabled', true);
                variantPrice.setAttribute('disabled', true);
                variantStatus.setAttribute('disabled', true);
                variantStock.setAttribute('disabled', true);
                btnEdit.textContent = 'Edit';
                variantProductBtn.classList.add('hidden');
                variantProductBtn.classList.remove('inline-flex');
                productStore.classList.add('rounded-r-md');
            } else {
                oldValue = {
                    name: productName.value,
                    description: productDescription.value,
                    store: productStore.value,
                    price: productPriceButton.value
                };
                btnEdit.textContent = 'Cancel';
                productName.removeAttribute('disabled');
                productDescription.removeAttribute('disabled');
                productStore.removeAttribute('disabled');
                productPriceButton.removeAttribute('disabled');
                productPriceTop.removeAttribute('disabled');
                productStoreBtn.classList.remove('hidden');
                productStoreBtn.classList.add('inline-flex');
                productStore.classList.remove('rounded-r-md');
            }
        }
        btnEdit.addEventListener('click', () => {
            editable = !editable;
            checkInput();
        });

        checkInput();
    };

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('input-group-search');
        const storeList = document.querySelector('#dropdownSearch ul');
        const storeItems = storeList.querySelectorAll('li');

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            storeItems.forEach(item => {
                const label = item.textContent.toLowerCase();
                item.style.display = label.includes(query) ? '' : 'none';
            });
        });

        storeItems.forEach(item => {
            item.addEventListener('click', function () {
                const radio = item.querySelector('input[type="radio"]');
                const label = item.querySelector('label');
                radio.checked = true;
                document.getElementById('variant-product').value = label.textContent.trim();
                document.getElementById('variant-product').setAttribute('data-store-id', radio.value);
                document.getElementById('dropdownSearch').classList.add('hidden');
            });
        });
    });
</script>
@endsection