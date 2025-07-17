@extends('layouts.main')
@section('title')
Products
@endsection

@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <div class="relative overflow-x-auto pt-1 shadow-md sm:rounded-lg">
        <div class="flex ps-1 pr-1 flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <x-input.input svg='<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>'
                id="search" type="text" placeholder='Product search'
                parentClass='w-100'/>
            <x-button.button1 id="btn-add" label='Add' color='green'
                type="button"  href="{{ route('products.create') }}" onclick="window.location.href='{{ route('stores.create') }}'"
                svg='<svg xmlns="http://www.w3.org/2000/svg"  width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="pr-1 lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>'/>
        </div>
        <x-table.table1 
            id="product-table"
            :data="$products"
            :columns_heads="['Name', 'Description', 'Stores']"
            :columns_bodys="['name', 'description', 'store_name']"
            :actions="['Edit' => fn($product) => route('products.edit', $product->id), 'Remove' => fn($product) => route('products.destroy', $product->id),]"
        />
    </div>
    <div class="flex items-center justify-end pt-4">
        {{ $products->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection
