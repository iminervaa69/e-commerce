{{-- Progressive Responsive navbar.blade.php --}}
<style>
    /* Custom breakpoints for progressive hiding */
    @media (max-width: 1279px) {
        .hide-xl { display: none !important; }
    }
    @media (max-width: 1023px) {
        .hide-lg { display: none !important; }
    }
    @media (max-width: 767px) {
        .hide-md { display: none !important; }
    }
    @media (max-width: 639px) {
        .hide-sm { display: none !important; }
    }
    @media (max-width: 479px) {
        .hide-xs { display: none !important; }
    }
</style>

<header>
    <nav class="fixed z-100 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 py-3 px-4">
        <div class="flex justify-between items-center max-w-screen-2xl mx-auto">
            <div class="flex justify-start items-center flex-1 min-w-0">
                <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
                    <img src="{{ asset('storage/photos/1/icon.png') }}" class="h-8 me-3" alt="RhodeShop Logo" />
                    <span class="self-center text-xl xl:text-2xl font-semibold whitespace-nowrap text-gray-900 dark:text-white transition-colors duration-300">RhodeShop</span>
                </a>

                <div id="main-search" class="ml-4 lg:ml-10 flex-1 max-w-2xl hide-md">
                    <x-form.search
                        containerClass='w-full'
                        id='search'
                        placeholder="Search products..."
                        inputClass=''
                    />
                </div>
            </div>

            <div class="flex items-center space-x-1 sm:space-x-2">
                <div id="main-notifications" class="relative hide-sm">
                    <button type="button"
                            id="notificationHoverTrigger"
                            class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-colors"
                            aria-expanded="false">
                        <span class="sr-only">Open notifications</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0"/>
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">3</span>
                    </button>

                    <x-navbar.notification-hover
                        position="center"
                        triggerId="notificationHover"
                    />
                </div>

                <div id="main-store" class="relative items-center hide-xl">
                    <button type="button"
                            id="storeHoverTrigger"
                            class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            aria-expanded="false">
                        <img class="w-8 h-8 rounded-full object-cover"
                             src="{{ Auth::user()->store_avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                             alt="Store profile">
                    </button>
                </div>
                <div class="hidden xl:block">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ Auth::user()->store_name ?? 'Store' }}</span>
                </div>

                <button type="button"
                        id="toggleMobileMenuButton"
                        class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-colors">
                    <span class="sr-only">Open menu</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div id="toggleMobileMenu" class="hidden fixed top-[73px] left-0 w-full bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50 shadow-lg">
        <div class="px-4 py-3 space-y-3">
            <div id="mobile-search-container" class="relative hidden">
                <x-form.search
                    containerClass='w-full'
                    id='mobile-search'
                    placeholder="Search products..."
                    inputClass=''
                />
            </div>

            <div id="mobile-menu-items" class="space-y-1">
                <button id="mobile-store" class="hidden w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                    <img class="w-6 h-6 rounded-full object-cover mr-3"
                         src="{{ Auth::user()->store_avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                         alt="Store profile">
                    <span>{{ Auth::user()->store_name ?? 'My Store' }}</span>
                </button>

                <div id="mobile-theme-toggle" class="hidden">
                    <x-navbar.theme-toggle
                        parentClass="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg"
                    />
                </div>

                <button id="mobile-notifications" class="hidden w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                        <path d="M10.268 21a2 2 0 0 0 3.464 0"/>
                        <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>
                    </svg>
                    <span>Notifications</span>
                    <span class="ml-auto inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">3</span>
                </button>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    class ResponsiveNavbar {
        constructor() {
            this.toggleButton = document.getElementById('toggleMobileMenuButton');
            this.mobileMenu = document.getElementById('toggleMobileMenu');
            this.breakpoints = {
                xs: 480,
                sm: 640,
                md: 768,
                lg: 1024,
                xl: 1280
            };

            this.init();
        }

        init() {
            this.updateMobileMenuItems();
            window.addEventListener('resize', () => this.updateMobileMenuItems());
            this.setupEventListeners();
            this.syncSearchInputs();
        }

        updateMobileMenuItems() {
            const width = window.innerWidth;
            let hasHiddenItems = false;

            const itemsToHide = [
                { breakpoint: this.breakpoints.xl, mainId: 'main-store', mobileId: 'mobile-store' },
                { breakpoint: this.breakpoints.lg, mainId: 'main-theme-toggle', mobileId: 'mobile-theme-toggle' },
                { breakpoint: this.breakpoints.md, mainId: 'main-search', mobileId: 'mobile-search-container' },
                { breakpoint: this.breakpoints.sm, mainId: 'main-notifications', mobileId: 'mobile-notifications' },
            ];

            itemsToHide.forEach(item => {
                const mainElement = document.getElementById(item.mainId);
                const mobileElement = document.getElementById(item.mobileId);

                if (mainElement && mobileElement) {
                    if (width < item.breakpoint) {
                        mainElement.classList.add('hidden');
                        mobileElement.classList.remove('hidden');
                        hasHiddenItems = true;
                    } else {
                        mainElement.classList.remove('hidden');
                        mobileElement.classList.add('hidden');
                    }
                }
            });

            this.toggleButton.style.display = hasHiddenItems ? 'flex' : 'none';
        }

        setupEventListeners() {
            this.toggleButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMobileMenu();
            });

            document.addEventListener('click', (e) => {
                if (!this.toggleButton.contains(e.target) && !this.mobileMenu.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1280) {
                    this.closeMobileMenu();
                }
            });

            this.setupUserDropdown();
        }

        setupUserDropdown() {
            const userDropdownTrigger = document.getElementById('userDropdownTrigger');
            const userDropdownMenu = document.getElementById('userDropdownMenu');

            if (userDropdownTrigger && userDropdownMenu) {
                userDropdownTrigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const isHidden = userDropdownMenu.classList.contains('hidden');

                    document.querySelectorAll('[id$="DropdownMenu"]').forEach(menu => {
                        if (menu !== userDropdownMenu) {
                            menu.classList.add('hidden');
                        }
                    });

                    if (isHidden) {
                        userDropdownMenu.classList.remove('hidden');
                        userDropdownTrigger.setAttribute('aria-expanded', 'true');
                    } else {
                        userDropdownMenu.classList.add('hidden');
                        userDropdownTrigger.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!userDropdownTrigger.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.add('hidden');
                        userDropdownTrigger.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        userDropdownMenu.classList.add('hidden');
                        userDropdownTrigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        }

        toggleMobileMenu() {
            const isHidden = this.mobileMenu.classList.contains('hidden');

            if (isHidden) {
                this.openMobileMenu();
            } else {
                this.closeMobileMenu();
            }
        }

        openMobileMenu() {
            this.mobileMenu.classList.remove('hidden');
            this.toggleButton.setAttribute('aria-expanded', 'true');

            const svg = this.toggleButton.querySelector('svg');
            svg.innerHTML = '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>';
        }

        closeMobileMenu() {
            this.mobileMenu.classList.add('hidden');
            this.toggleButton.setAttribute('aria-expanded', 'false');

            const svg = this.toggleButton.querySelector('svg');
            svg.innerHTML = '<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>';
        }

        syncSearchInputs() {
            const mainSearchInput = document.querySelector('#main-search input') || document.getElementById('search');
            const mobileSearchInput = document.querySelector('#mobile-search-container input') || document.getElementById('mobile-search');

            if (mainSearchInput && mobileSearchInput) {
                mainSearchInput.addEventListener('input', (e) => {
                    mobileSearchInput.value = e.target.value;
                });

                mobileSearchInput.addEventListener('input', (e) => {
                    mainSearchInput.value = e.target.value;
                });

                const mainSearchForm = mainSearchInput.closest('form');
                const mobileSearchForm = mobileSearchInput.closest('form');

                if (mainSearchForm && mobileSearchForm) {
                    mainSearchForm.addEventListener('submit', (e) => {
                        mobileSearchInput.value = mainSearchInput.value;
                    });

                    mobileSearchForm.addEventListener('submit', (e) => {
                        mainSearchInput.value = mobileSearchInput.value;
                    });
                }
            }
        }
    }

    new ResponsiveNavbar();

    window.updateNotificationCount = function() {
        fetch('/api/notifications/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const notificationBadges = document.querySelectorAll('#main-notifications .bg-red-500, #mobile-notifications .bg-red-500');
            notificationBadges.forEach(badge => {
                badge.textContent = data.count || '0';
                badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
            });
        })
        .catch(error => console.error('Error updating notification count:', error));
    };
});
</script>
