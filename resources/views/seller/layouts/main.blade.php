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
    <div class="relative z-40">
        @include('seller.layouts.navbar')
    </div>

    <div id="mainContent" class="sm:ml-64">
        <div class="p-4 pt-20">
            <div class="relative z-30">
                @include('seller.layouts.sidebar')
            </div>
            @yield('content')
        </div>
    </div>

    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.getElementById('mainContent');

            document.addEventListener('sidebarCollapsed', function(e) {
                if (window.innerWidth >= 640) {
                    if (e.detail.collapsed) {
                        mainContent.classList.remove('sm:ml-64');
                        mainContent.classList.add('sm:ml-16');
                    } else {
                        mainContent.classList.remove('sm:ml-16');
                        mainContent.classList.add('sm:ml-64');
                    }
                }
            });

            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (window.innerWidth >= 640) {
                if (isCollapsed) {
                    mainContent.classList.remove('sm:ml-64');
                    mainContent.classList.add('sm:ml-16');
                } else {
                    mainContent.classList.remove('sm:ml-16');
                    mainContent.classList.add('sm:ml-64');
                }
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth < 640) {
                    mainContent.classList.remove('sm:ml-16', 'sm:ml-64');
                } else {
                    const currentCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (currentCollapsed) {
                        mainContent.classList.remove('sm:ml-64');
                        mainContent.classList.add('sm:ml-16');
                    } else {
                        mainContent.classList.remove('sm:ml-16');
                        mainContent.classList.add('sm:ml-64');
                    }
                }
            });
        });
    </script>

    @yield('insert-scripts')
    @stack('scripts')

</body>

</html>
