@props(['parentClass' => ''])

<div x-data="themeToggle()" x-init="init()" class="@twMerge('relative', $parentClass ?? '')">
    <button
        @click="toggleTheme()"
        id="theme-toggle"
        data-tooltip-target="tooltip-toggle"
        type="button"
        class="text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 rounded-lg text-sm p-2.5 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 transition-colors duration-300"
        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <!-- Moon icon for dark mode -->
        <svg x-show="!isDark" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="theme-toggle-dark-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon">
            <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/>
        </svg>
        <!-- Sun icon for light mode -->
        <svg x-show="isDark" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="theme-toggle-light-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun">
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
        </svg>
    </button>

    <!-- Optional: Uncomment for tooltip -->
    {{-- <div id="tooltip-toggle" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700">
        <span x-text="isDark ? 'Switch to light mode' : 'Switch to dark mode'"></span>
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div> --}}
</div>

<script>
    function themeToggle() {
        return {
            isDark: false,

            init() {
                const storedTheme = this.getStoredTheme();
                
                // Initialize theme state
                if (storedTheme) {
                    // User has made a choice, respect it
                    this.isDark = storedTheme === 'dark';
                } else {
                    // No stored preference, use system preference
                    this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                
                // Apply initial theme
                this.applyTheme();

                // Listen for system preference changes (only if no stored preference)
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    if (!this.getStoredTheme()) {
                        this.isDark = e.matches;
                        this.applyTheme();
                    }
                });
            },

            getStoredTheme() {
                return localStorage.getItem('color-theme');
            },

            applyTheme() {
                if (this.isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            },

            toggleTheme() {
                this.isDark = !this.isDark;
                
                // Store preference
                localStorage.setItem('color-theme', this.isDark ? 'dark' : 'light');
                
                // Apply theme
                this.applyTheme();

                // Dispatch custom event for other components
                window.dispatchEvent(new CustomEvent('dark-mode-changed', {
                    detail: { isDark: this.isDark }
                }));
            }
        }
    }
</script>