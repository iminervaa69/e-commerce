<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    <div class="relative z-50">
        @include('frontend.layouts.navbar')
    </div>

    <div class="p-4 z-10 h-max-screen">
        <div class="bg-white dark:bg-gray-800 transition-colors duration-300">
            @yield('content')
        </div>
    </div>

    <div class="relative z-50">
        @include('frontend.layouts.footer')
    </div>

    <!-- Load scripts in proper order -->
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    {{-- <!-- CSRF token setup for AJAX -->
    <script>
        // Set up CSRF token for all AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            // Or for jQuery if you're using it
            // $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
        }
    </script> --}}

    @yield('insert-scripts')
    @stack('scripts')

</body>

</html>
