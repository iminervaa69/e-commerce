function themeToggle() {
    return {
        isDark: false,

        init() {
            // Get current state
            const storedTheme = localStorage.getItem('color-theme');
            
            if (storedTheme) {
                this.isDark = storedTheme === 'dark';
            } else {
                this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            
            // Apply theme instantly without transitions
            this.applyThemeInstant();

            // Listen for system changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('color-theme')) {
                    this.isDark = e.matches;
                    this.applyThemeInstant();
                }
            });
        },

        applyThemeInstant() {
            // Apply immediately without any transitions
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        toggleTheme() {
            // Update state instantly
            this.isDark = !this.isDark;
            
            // Store preference
            localStorage.setItem('color-theme', this.isDark ? 'dark' : 'light');
            
            // Apply instantly - no transitions during toggle
            this.applyThemeInstant();

            // Dispatch event
            window.dispatchEvent(new CustomEvent('dark-mode-changed', {
                detail: { isDark: this.isDark }
            }));
        }
    }
}