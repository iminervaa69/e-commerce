@props([
    'title' => 'Are you sure?',
    'description' => 'This action cannot be undone.',
    'action' => '#',
    'method' => 'POST',
])

<div x-data="{ open: false }" class="inline-block">
    <button 
        @click="open=true" 
        type="button"
        class="text-sm text-red-600 hover:underline">
        Delete
    </button>

    <div 
        x-show="open" 
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center darkbg-opacity-50"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <div class="flex items-center mb-4 text-red-600">
                <svg class="shrink-0 w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
            </div>
            <p class="text-sm text-gray-600 mb-6">
                {{ $description }}
            </p>
            <div class="flex justify-end space-x-2">
                <button @click="open=false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Cancel
                </button>
                <form method="POST" action="{{ $action }}">
                    @csrf
                    @method($method)
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Confirm Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
