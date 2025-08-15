@props([
    'mainImage',
    'thumbnails' => [],
    'alt' => '',
    'productName' => ''
])

<div class="flex flex-col space-y-4">
    <div class="bg-black rounded-lg overflow-hidden aspect-square flex items-center justify-center">
        <img 
            src="{{ $mainImage }}" 
            alt="{{ $alt ?: $productName }}"
            class="max-w-full max-h-full object-contain"
            id="main-product-image"
        >
    </div>
    
    @if(count($thumbnails) > 0)
        <div class="flex space-x-2">
            @foreach($thumbnails as $index => $thumbnail)
                <button 
                    class="w-16 h-16 bg-gray-100 rounded border-2 border-transparent hover:border-blue-500 focus:border-blue-500 overflow-hidden"
                    onclick="changeMainImage('{{ $thumbnail['url'] }}')"
                >
                    <img 
                        src="{{ $thumbnail['url'] }}" 
                        alt="{{ $thumbnail['alt'] ?? "Thumbnail $index" }}"
                        class="w-full h-full object-cover"
                    >
                </button>
            @endforeach
        </div>
    @endif
</div>

<script>
function changeMainImage(newSrc) {
    document.getElementById('main-product-image').src = newSrc;
}
</script>