@extends('layouts.main')
@section('title')
Categories
@endsection

@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
<div class="relative overflow-x-auto pt-1 shadow-md sm:rounded-lg">
    <div class="flex ps-1 pr-1 flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <x-input.input svg='<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>'
            id="search" type="text" placeholder='Category search'
            parentClass='w-100'/>
        <x-button.button1 id="btn-add" label='Add' color='green'
            type="button"  href="{{ route('categories.create') }}" onclick="window.location.href='{{ route('stores.create') }}'"
            svg='<svg xmlns="http://www.w3.org/2000/svg"  width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="pr-1 lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>'/>
    </div>
    <x-table.table1 
        id="category-table"
        :data="$categories"
        :columns_heads="['Name']"
        :columns_bodys="['name']"
        :actions="['Edit' => fn($category) => route('categories.edit', $category->id), 'Remove' => fn($category) => route('categories.destroy', $category->id),]"
    />
</div>
<div>
    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Showing <span class="font-semibold text-gray-900 dark:text-white">1-10</span> of <span class="font-semibold text-gray-900 dark:text-white">1000</span></span>
        <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
            </li>
            <li>
                <a href="#" aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">1</a>
            </li>
            <li>
                <a href="#" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>
            </li>
        </ul>
    </nav>
</div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('store-search');
        const storeList = document.querySelector('#dropdownSearch ul');
        const storeItems = storeList.querySelectorAll('li');

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            storeItems.forEach(item => {
                const label = item.textContent.toLowerCase();
                item.style.display = label.includes(query) ? '' : 'none';
            });
        }); 

        initEditable();
    });
</script>
@endsection
