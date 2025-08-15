<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark mode initialization script - must be in head to prevent flash -->
    <script>
        // Check for saved theme preference or default to 'light'
        if (localStorage.getItem('color-theme') === 'dark' ||
            (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    @include('layouts.navbar')
    @include('layouts.sidebar')

    <div class="p-4 sm:ml-64">
        <div class="bg-white dark:bg-gray-800 transition-colors duration-300">
            @yield('content')
        </div>
    </div>

    <!-- Dark mode toggle component -->
    <div x-data="darkModeToggle()" class="fixed bottom-4 right-4 z-50">
        <button
            @click="toggleDarkMode()"
            class="p-3 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300 shadow-lg"
            aria-label="Toggle dark mode"
        >
            <!-- Sun icon for light mode -->
            <svg x-show="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
            <!-- Moon icon for dark mode -->
            <svg x-show="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
        </button>
    </div>

    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>

    <!-- Alpine.js dark mode component -->
    <script>
        function darkModeToggle() {
            return {
                isDark: localStorage.getItem('color-theme') === 'dark' ||
                       (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),

                toggleDarkMode() {
                    this.isDark = !this.isDark;

                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }

                    // Dispatch custom event for other components to listen to
                    window.dispatchEvent(new CustomEvent('dark-mode-changed', {
                        detail: { isDark: this.isDark }
                    }));
                }
            }
        }

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('color-theme')) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    </script>

    @yield('insert-scripts')

</body>

</html>
