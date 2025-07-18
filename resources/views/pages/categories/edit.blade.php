@extends('layouts.main')
@section('title')
Edit Category - {{ $category->name }}
@endsection

@section('content')
<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between">
        <p class="block mb-2 text-m ms-2 font-medium text-gray-900 dark:text-white">Category Details</p>
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-end pb-4">
            <x-button.button1 href="{{ route('categories.index') }}" id="btn-back" label='Back' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg"  class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>'/>
            <x-button.button1 id="btn-edit" label='Edit' color='gray'
                svg='<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5 mr-1.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>'/>
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <form class="mx-auto h-full" action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex gap-10 w-full p-4">
                <div class="w-1/2">
                    <x-input.label for="name" label="Category Name"/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store-icon lucide-store"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>'
                        id="category-name" type="text" name="name" 
                        value='{{ $category->name }}' placeholder='Category Name' required />
                </div>
                <div class="w-1/2">
                    <x-input.label for="description" label="Category Description"/>
                    <x-input.input svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>' 
                        id="category-description" type="text" name="description" 
                        value='{{ $category->description }}' placeholder='Category description' required />
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
@endsection

@section('insert-scripts')
<script>
let editable = false;
let oldValue = null;

const initEditable = () => {
    const btnEdit = document.getElementById('btn-edit');
    const btnCancel = document.getElementById('btn-cancel');

    const checkInput = () => {
        const categoryName = document.getElementById('category-name');
        const categoryDescription = document.getElementById('category-description');
        const saveGroup = document.getElementById('save-group');

        if (!editable) {
            if (oldValue) {
                categoryName.value = oldValue.name;
                categoryDescription.value = oldValue.description;
            }

            categoryName.setAttribute('disabled', true);
            categoryDescription.setAttribute('disabled', true);
            btnEdit.removeAttribute('disabled');
            saveGroup.classList.add('hidden');
        } else {
            oldValue = {
                name: categoryName.value,
                description: categoryDescription.value
            };

            categoryName.removeAttribute('disabled');
            categoryDescription.removeAttribute('disabled');
            btnEdit.setAttribute('disabled', true);
            saveGroup.classList.remove('hidden');
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