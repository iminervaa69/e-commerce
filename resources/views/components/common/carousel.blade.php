{{-- resources/views/components/owl-carousel.blade.php --}}
@props([
    'items' => [],
    'id' => 'owl-carousel-' . uniqid(),
    'height' => 'h-56 md:h-96',
    'showDots' => true,
    'showNav' => true,
    'autoPlay' => false,
    'autoPlayTimeout' => 5000,
    'autoPlayHoverPause' => true,
    'loop' => true,
    'responsive' => [
        '0' => ['items' => 1],
        '768' => ['items' => 1],
        '1024' => ['items' => 1]
    ],
    'animateOut' => 'fadeOut',
    'animateIn' => 'fadeIn',
    'smartSpeed' => 450,
    'autoHeight' => false,
    'parentClass' => ''
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <!-- Don't include default theme to avoid double dots -->
    <!-- <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}"> -->
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    /* Reset any default owl theme styles */
    .owl-theme .owl-dots {
        display: none !important;
    }
    
    .owl-theme .owl-nav {
        display: none !important;
    }
    
    /* Force show our custom navigation */
    .owl-carousel .owl-nav {
        display: block !important;
    }
    
    /* Navigation buttons - side positioning like original */
    .owl-carousel .owl-nav button.owl-prev,
    .owl-carousel .owl-nav button.owl-next {
        position: absolute;
        top: 0;
        z-index: 500;
        display: flex !important; /* Force display to override owl theme */
        align-items: center;
        justify-content: center;
        height: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
        cursor: pointer;
        background: transparent;
        border: none;
        outline: none;
        opacity: 1 !important; /* Ensure buttons are visible */
    }
    
    .owl-carousel .owl-nav button.owl-prev {
        left: 0;
    }
    
    .owl-carousel .owl-nav button.owl-next {
        right: 0;
    }
    
    /* Navigation button icons - matching original style */
    .owl-carousel .owl-nav button span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.3);
        color: white;
        transition: all 0.3s ease;
        pointer-events: none; /* Prevent span from interfering with button clicks */
    }
    
    .dark .owl-carousel .owl-nav button span {
        background-color: rgba(31, 41, 55, 0.3);
        color: rgb(31, 41, 55);
    }
    
    .owl-carousel .owl-nav button:hover span {
        background-color: rgba(255, 255, 255, 0.5);
    }
    
    .dark .owl-carousel .owl-nav button:hover span {
        background-color: rgba(31, 41, 55, 0.6);
    }
    
    .owl-carousel .owl-nav button:focus span {
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.5);
    }
    
    .dark .owl-carousel .owl-nav button:focus span {
        box-shadow: 0 0 0 4px rgba(31, 41, 55, 0.7);
    }
    
    .owl-carousel .owl-dots {
        position: absolute;
        bottom: 1.25rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 30;
        display: flex !important;
        gap: 0.5rem;
    }
    
    .owl-carousel .owl-dots .owl-dot {
        height: 0.25rem;
        width: 1rem;
        border-radius: 9999px;
        background-color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .owl-carousel .owl-dots .owl-dot:hover {
        background-color: rgba(255, 255, 255, 0.75);
    }
    
    .owl-carousel .owl-dots .owl-dot.active {
        background-color: white;
        width: 2rem; /* Longer active dot like original */
    }
    
    /* Carousel container            styling */
    .owl-carousel .item {
        position: relative;
        overflow: hidden;
        border-radius: 0.5rem;
    }
    
    /* Ensure proper positioning */
    .owl-carousel {
        position: relative;
        width: 100%;
    }

    /* Screen reader only text for accessibility */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>
@endpush

<div class="relative {{ $parentClass ?? '' }}">
    <div id="{{ $id }}" class="owl-carousel owl-theme {{ $height }}" role="region" aria-label="Image carousel">
        @foreach($items as $index => $item)
            <div class="item relative {{ $height }}">
                @if(isset($item['type']) && $item['type'] === 'content')
                    <!-- Custom content slide -->
                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                        {!! $item['content'] !!}
                    </div>
                @else
                    <!-- Image slide -->
                    <img src="{{ $item['src'] ?? $item }}" 
                        class="w-full h-full object-cover object-center" 
                        alt="{{ $item['alt'] ?? 'Slide ' . ($index + 1) }}"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                @endif
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script>
        // Ensure no jQuery conflicts
        jQuery(document).ready(function($) {
            // Check if carousel already initialized to prevent double initialization
            if ($("#{{ $id }}").hasClass('owl-loaded')) {
                $("#{{ $id }}").trigger('destroy.owl.carousel');
            }
            
            $("#{{ $id }}").owlCarousel({
                items: 1,
                loop: true,
                margin: 0,
                nav: {{ $showNav ? 'true' : 'false' }},
                dots: {{ $showDots ? 'true' : 'false' }},
                autoplay: {{ $autoPlay ? 'true' : 'false' }},
                autoplayTimeout: {{ $autoPlayTimeout }},
                autoplayHoverPause: {{ $autoPlayHoverPause ? 'true' : 'false' }},
                @if($animateOut && $animateIn)
                animateOut: 'animate__{{ $animateOut }}',
                animateIn: 'animate__{{ $animateIn }}',
                @endif
                smartSpeed: {{ $smartSpeed }},
                autoHeight: {{ $autoHeight ? 'true' : 'false' }},
                responsive: {!! json_encode($responsive) !!},
                navText: [
                    '<span><svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/></svg></span><span class="sr-only">Previous slide</span>',
                    '<span><svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg></span><span class="sr-only">Next slide</span>'
                ],
                onInitialized: function(event) {
                    console.log('Owl Carousel initialized for #{{ $id }}');
                    
                    // Add proper accessibility attributes to navigation buttons
                    const carousel = $("#{{ $id }}");
                    const prevBtn = carousel.find('.owl-prev');
                    const nextBtn = carousel.find('.owl-next');
                    
                    // Remove problematic role="presentation" and add proper attributes
                    prevBtn.removeAttr('role').attr({
                        'aria-label': 'Previous slide',
                        'title': 'Previous slide'
                    });
                    
                    nextBtn.removeAttr('role').attr({
                        'aria-label': 'Next slide',
                        'title': 'Next slide'
                    });
                    
                    // Add aria-labels to dots for better accessibility
                    carousel.find('.owl-dot').each(function(index) {
                        $(this).attr({
                            'aria-label': 'Go to slide ' + (index + 1),
                            'title': 'Go to slide ' + (index + 1)
                        });
                    });
                },
                onChanged: function(event) {
                    console.log('Slide changed for #{{ $id }}');
                    
                    // Update aria-labels for dots when slide changes
                    const carousel = $("#{{ $id }}");
                    carousel.find('.owl-dot').each(function(index) {
                        const isActive = $(this).hasClass('active');
                        $(this).attr('aria-label', (isActive ? 'Current slide ' : 'Go to slide ') + (index + 1));
                    });
                }
            });
        });
    </script>
@endpush