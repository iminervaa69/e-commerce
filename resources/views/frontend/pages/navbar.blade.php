<style>
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

                <div id="main-search" class="ml-4 lg:ml-10 flex-1 max-w-2xl hide-xs">
                    <x-form.search
                        containerClass='w-full'
                        id='search'
                        placeholder="Search products..."
                        inputClass=''
                    />
                </div>
            </div>

            <div class="flex items-center space-x-1 sm:space-x-2">
                <div id="main-cart" class="relative hide-md">
                    <button type="button"
                            class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-colors"
                            aria-expanded="false"
                            onclick="window.location.href='{{ route('cart.index') }}'">
                        <span class="sr-only">Open cart</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart">
                            <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">{{ $cartCount ?? '0' }}</span>
                    </button>
                </div>

                <div id="main-notifications" class="relative hide-md">
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

                <div id="main-theme-toggle" class="hide-lg">
                    <x-navbar.theme-toggle
                        parentClass=""
                    />
                </div>

                <div id="main-store" class="relative flex items-center hide-xl">
                    <div class="hidden xl:block ml-4 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ Auth::user()->store_name ?? 'Store' }}</span>
                    </div>
                    <button type="button"
                            id="storeHoverTrigger"
                            class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            aria-expanded="false">
                        <img class="w-8 h-8 rounded-full object-cover"
                             src="{{ Auth::user()->store_avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                             alt="Store profile">
                    </button>
                </div>

                <div  class="relative flex items-center">
                    <div class="hidden lg:block ml-4 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ Auth::user()->name ?? 'User' }}</span>
                    </div>
                    <div id="main-user" class="relative ">
                        <button type="button"
                                id="userHoverTrigger"
                                class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false">
                            <img class="w-8 h-8 rounded-full object-cover"
                                src="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                                alt="User profile">
                        </button>

                        <x-navbar.profile-hover
                            :user="Auth::user()"
                            position="right"
                            triggerId="userHover"
                        />
                    </div>
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

                <button id="mobile-cart" class="hidden w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg"
                        onclick="window.location.href='{{ route('cart.index') }}'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                        <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                    </svg>
                    <span>Cart</span>
                    <span class="ml-auto inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">{{ $cartCount ?? '0' }}</span>
                </button>
            </div>

            <div id="mobile-user-profile" class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                <div class="flex items-center px-3 py-2">
                    <img class="w-8 h-8 rounded-full object-cover mr-3"
                         src="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                         alt="User profile">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name ?? 'User Name' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Auth::user()->email ?? 'user@example.com' }}
                        </p>
                    </div>
                </div>

                <div class="px-3 pb-2 space-y-1">
                    <a  class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        Profile
                    </a>

                    <a  class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        Settings
                    </a>

                    <a  class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        My Orders
                    </a>

                    <a  class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                        Wishlist
                    </a>

                    <a  class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        Help & Support
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                            <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
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

            // Define what should be hidden at each breakpoint (progressive)
            const itemsToHide = [
                { breakpoint: this.breakpoints.xl, mainId: 'main-store', mobileId: 'mobile-store' },
                { breakpoint: this.breakpoints.lg, mainId: 'main-theme-toggle', mobileId: 'mobile-theme-toggle' },
                { breakpoint: this.breakpoints.md, mainId: 'main-search', mobileId: 'mobile-search-container' },
                { breakpoint: this.breakpoints.sm, mainId: 'main-notifications', mobileId: 'mobile-notifications' },
                { breakpoint: this.breakpoints.xs, mainId: 'main-cart', mobileId: 'mobile-cart' }
            ];

            itemsToHide.forEach(item => {
                const mainElement = document.getElementById(item.mainId);
                const mobileElement = document.getElementById(item.mobileId);

                if (mainElement && mobileElement) {
                    if (width < item.breakpoint) {
                        // Hide main item and show mobile item
                        mainElement.classList.add('hidden');
                        mobileElement.classList.remove('hidden');
                        hasHiddenItems = true;
                    } else {
                        // Show main item and hide mobile item
                        mainElement.classList.remove('hidden');
                        mobileElement.classList.add('hidden');
                    }
                }
            });

            // Show/hide mobile menu toggle button
            this.toggleButton.style.display = hasHiddenItems ? 'flex' : 'none';
        }

        setupEventListeners() {
            // Mobile menu toggle
            this.toggleButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMobileMenu();
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.toggleButton.contains(e.target) && !this.mobileMenu.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });

            // Close mobile menu on window resize if needed
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1280) {
                    this.closeMobileMenu();
                }
            });

            // User dropdown functionality
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

                    // Close all other dropdowns first
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

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!userDropdownTrigger.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.add('hidden');
                        userDropdownTrigger.setAttribute('aria-expanded', 'false');
                    }
                });

                // Close dropdown on escape key
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

            // Change to X icon
            const svg = this.toggleButton.querySelector('svg');
            svg.innerHTML = '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>';
        }

        closeMobileMenu() {
            this.mobileMenu.classList.add('hidden');
            this.toggleButton.setAttribute('aria-expanded', 'false');

            // Change back to hamburger icon
            const svg = this.toggleButton.querySelector('svg');
            svg.innerHTML = '<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>';
        }

        syncSearchInputs() {
            // Get search inputs - handle both regular inputs and Laravel components
            const mainSearchInput = document.querySelector('#main-search input') || document.getElementById('search');
            const mobileSearchInput = document.querySelector('#mobile-search-container input') || document.getElementById('mobile-search');

            if (mainSearchInput && mobileSearchInput) {
                // Sync main to mobile
                mainSearchInput.addEventListener('input', (e) => {
                    mobileSearchInput.value = e.target.value;
                });

                // Sync mobile to main
                mobileSearchInput.addEventListener('input', (e) => {
                    mainSearchInput.value = e.target.value;
                });

                // Handle form submissions
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

    // Initialize the responsive navbar
    new ResponsiveNavbar();

    // AJAX function for updating cart count (if needed)
    window.updateCartCount = function() {
        fetch('/cart/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const cartBadges = document.querySelectorAll('#main-cart .bg-red-500, #mobile-cart .bg-red-500');
            cartBadges.forEach(badge => {
                badge.textContent = data.count || '0';
                badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
            });
        })
        .catch(error => console.error('Error updating cart count:', error));
    };

    // AJAX function for updating notification count (if needed)
    window.updateNotificationCount = function() {
        fetch('/notifications/count', {
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
