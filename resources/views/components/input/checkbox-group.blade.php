{{-- resources/views/components/checkbox-group.blade.php --}}
@props([
    'name',
    'options' => [],
    'selected' => [],
    'showStars' => false,
    'vertical' => true
])

<div class="space-y-2">
    @foreach($options as $option)
        @php
            $value = is_array($option) ? $option['value'] : $option;
            $label = is_array($option) ? $option['label'] : $option;
            $stars = is_array($option) && isset($option['stars']) ? $option['stars'] : null;
            $isChecked = in_array($value, (array) $selected);
            $checkboxId = $name . '-' . Str::slug($value);
        @endphp
        
        <label 
            for="{{ $checkboxId }}" 
            class="flex items-center cursor-pointer group hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md p-1 -m-1 transition-colors duration-150"
        >
            <input 
                type="checkbox" 
                id="{{ $checkboxId }}"
                name="{{ $name }}[]" 
                value="{{ $value }}"
                {{ $isChecked ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-2 transition-colors duration-150"
            >
            
            <span class="ml-3 flex items-center">
                @if($showStars && $stars)
                    <div class="flex items-center mr-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $stars ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" 
                                 fill="currentColor" 
                                 viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $stars }}</span>
                    </div>
                @endif
                
                <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors duration-150">
                    {{ $label }}
                </span>
            </span>
        </label>
    @endforeach
</div>