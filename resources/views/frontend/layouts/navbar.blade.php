
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

    .mobile-menu-enter {
        transform: translateY(-100%);
        opacity: 0;
    }

    .mobile-menu-active {
        transform: translateY(0);
        opacity: 1;
        transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .mobile-menu-exit {
        transform: translateY(-100%);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    /* Menu item hover animations */
    .mobile-menu-item {
        transition: all 0.2s ease-in-out;
    }

    .mobile-menu-item:hover {
        transform: translateX(4px);
        background: linear-gradient(90deg, rgba(6, 182, 212, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
    }

    /* Badge pulse animation */
    .badge-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .7;
        }
    }

    /* Backdrop blur effect */
    .backdrop-blur-md {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
</style>

<header>
    <nav class="fixed z-100 w-full bg-white/95 backdrop-blur-md border-b border-gray-200 dark:bg-gray-800/95 dark:border-gray-700 py-3 px-4 shadow-sm">
        <div class="flex justify-between items-center max-w-screen-2xl mx-auto">
            <div class="flex justify-start items-center flex-1 min-w-0 mr-4">
                <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
                    <img src="{{ asset('storage/photos/1/icon.png') }}" class="h-8 me-3" alt="RhodeShop Logo" />
                    <span class="self-center text-xl xl:text-2xl font-semibold whitespace-nowrap text-gray-900 dark:text-white transition-colors duration-300">RhodeShop</span>
                </a>

                <div id="main-search" class="ml-4 lg:ml-10 flex-1 hide-sm">
                    <x-form.search
                        containerClass='w-full'
                        id='search'
                        placeholder="Search products..."
                        inputClass='focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500'
                    />
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-2">
                @auth
                <div id="main-notifications" class="relative hide-sm">
                    <button type="button"
                            id="notificationHoverTrigger"
                            class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200"
                            aria-expanded="false">
                        <span class="sr-only">Open notifications</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0"/>
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full badge-pulse">3</span>
                    </button>

                    <x-navbar.notification-hover
                        position="center"
                        triggerId="notificationHover"
                    />
                </div>
                @endauth

                <div id="main-cart" class="relative hide-sm">
                    <button type="button"
                            class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200"
                            aria-expanded="false"
                            onclick="window.location.href='{{ route('cart.index') }}'">
                        <span class="sr-only">Open cart</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart">
                            <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full badge-pulse">{{ $cartCount ?? '0' }}</span>
                    </button>
                </div>

                <div id="main-theme-toggle" class="relative hide">
                </div>

                @auth
                <div id="profile" class="flex items-center space-x-2">
                    <div id="main-store" class="relative items-center hide-xl">
                        <button type="button"
                                id="storeHoverTrigger"
                                class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 hover:ring-2 hover:ring-cyan-500 transition-all duration-200"
                                aria-expanded="false">
                            <img class="w-8 h-8 rounded-full object-cover"
                                src="{{ Auth::user()->store_avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                                alt="Store profile">
                        </button>
                    </div>
                    <div class="hidden xl:block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->store_name ?? 'Store' }}</span>
                    </div>

                    <div id="main-user" class="relative items-center">
                        <button type="button"
                                id="userHoverTrigger"
                                class="flex items-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 hover:ring-2 hover:ring-cyan-500 transition-all duration-200"
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
                    <div class="hidden lg:block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name ?? 'User' }}</span>
                    </div>
                </div>
                @else
                <div id="auth-buttons" class="flex items-center space-x-2 hide-sm">
                    <button onclick="window.location.href='{{ route('login') }}'"
                            class="px-5 py-1.5 border border-cyan-500 bg-cyan-500 text-white text-sm font-semibold rounded-md hover:bg-cyan-600 hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                        Login
                    </button>
                    <button onclick="window.location.href='{{ route('register') }}'"
                            class="px-3 py-1.5 border border-cyan-500 text-cyan-500 text-sm font-semibold rounded-md hover:bg-cyan-500 hover:text-white hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                        Register
                    </button>
                </div>
                @endauth

                <button type="button"
                        id="toggleMobileMenuButton"
                        class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200">
                    <span class="sr-only">Open menu</span>
                    <svg id="hamburgerIcon" class="w-6 h-6 transition-transform duration-300" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <svg id="closeIcon" class="w-6 h-6 hidden transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div id="toggleMobileMenu" class="hidden fixed top-[65px] left-0 w-full bg-white/95 backdrop-blur-md dark:bg-gray-800/95 border-b border-gray-200 dark:border-gray-700 z-50 shadow-2xl mobile-menu-enter">
        <div class="max-h-[calc(100vh-73px)] overflow-y-auto">
            <div id="mobile-search-container" class="hidden px-4 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50/50 to-cyan-50/50 dark:from-gray-700/50 dark:to-gray-600/50">
                <div class="space-y-2">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Quick Search</h3>
                    <x-form.search
                        containerClass='w-full'
                        id='mobile-search'
                        placeholder="Search products..."
                        inputClass='py-3 text-base rounded-xl border-gray-300 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm'
                    />
                </div>
            </div>

            @auth
            <div class="px-4 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center space-x-4 mb-4 p-3 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl">
                    <img class="w-12 h-12 rounded-full object-cover border-2 border-cyan-200 dark:border-cyan-600 shadow-md"
                         src="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                         alt="User profile">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-base">{{ Auth::user()->name ?? 'User' }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Welcome back!</p>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Online</span>
                    </div>
                </div>
            </div>
            @endauth

            <div id="mobile-menu-items" class="px-4 py-2">
                <button id="mobile-cart" class="hidden w-full mobile-menu-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group mb-2"
                        onclick="window.location.href='{{ route('cart.index') }}'">
                    <div class="relative p-2 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-green-600 dark:text-green-400">
                            <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-green-500 rounded-full badge-pulse">{{ $cartCount ?? '0' }}</span>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">Shopping Cart</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cartCount ?? '0' }} items ready</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button x-data="themeToggle()" x-init="init()" id="mobile-theme-toggle" class="hidden w-full mobile-menu-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group mb-2"
                        @click="toggleTheme()">
                    <div class="relative p-2 bg-gradient-to-br from-white-100 to-white-100 dark:from-white-900/30 dark:to-white-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                        <svg x-show="!isDark" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="theme-toggle-dark-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon">
                            <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/>
                        </svg>
                        <!-- Sun icon for light mode -->
                        <svg x-show="isDark" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="theme-toggle-light-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun">
                            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">Mode</p>
                        <p x-show="!isDark" class="text-xs text-gray-500 dark:text-gray-400">Current mode: Light</p>
                        <p x-show="isDark" class="text-xs text-gray-500 dark:text-gray-400">Current mode: Dark</p>
                    </div>
                </button>

            <div class="space-y-1 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                @auth
                <button id="mobile-store" class="hidden w-full mobile-menu-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group mb-2">
                    <div class="relative p-2 bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                        <img class="w-6 h-6 rounded-full object-cover"
                             src="{{ Auth::user()->store_avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                             alt="Store profile">
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">{{ Auth::user()->store_name ?? 'My Store' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage your store</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button id="mobile-notifications" class="hidden w-full mobile-menu-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group mb-2">
                    <div class="relative p-2 bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-600 dark:text-red-400">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0"/>
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full badge-pulse">3</span>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">Notifications</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">3 new messages</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button class="w-full mobile-menu-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group">
                    <div class="p-2 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600 dark:text-blue-400">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">My Orders</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Track your purchases</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button class="w-full mobile-menu-item flex items-center px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group">
                    <div class="p-2 bg-gradient-to-br from-pink-100 to-rose-100 dark:from-pink-900/30 dark:to-rose-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-pink-600 dark:text-pink-400">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">Wishlist</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Saved favorites</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>


                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full mobile-menu-item flex items-center px-4 py-3 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl group">
                        <div class="p-2 bg-gradient-to-br from-red-100 to-pink-100 dark:from-red-900/30 dark:to-pink-900/30 rounded-lg mr-4 group-hover:scale-110 transition-transform duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-600 dark:text-red-400">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16,17 21,12 16,7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="font-medium">Sign Out</p>
                            <p class="text-xs text-red-400 dark:text-red-300">See you later!</p>
                        </div>
                    </button>
                </form>
                @else
                <div class="space-y-2">
                    <button onclick="window.location.href='{{ route('login') }}'"
                            class="w-full mobile-menu-item flex items-center justify-center px-4 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                            <path d="M15 3h6v6"/>
                            <path d="M10 14 21 3"/>
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        </svg>
                        <span class="font-semibold">Sign In</span>
                    </button>

                    <button onclick="window.location.href='{{ route('register') }}'"
                            class="w-full mobile-menu-item flex items-center justify-center px-4 py-3 border-2 border-cyan-500 text-cyan-600 dark:text-cyan-400 rounded-xl hover:bg-cyan-500 hover:text-white transform hover:scale-105 transition-all duration-200 group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                        <span class="font-semibold">Create Account</span>
                    </button>
                </div>
                @endauth
            </div>
        </div>
    </div>
</header>

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

document.addEventListener('DOMContentLoaded', function() {
    class EnhancedResponsiveNavbar {
        constructor() {
            this.toggleButton = document.getElementById('toggleMobileMenuButton');
            this.mobileMenu = document.getElementById('toggleMobileMenu');
            this.hamburgerIcon = document.getElementById('hamburgerIcon');
            this.closeIcon = document.getElementById('closeIcon');
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
            this.setupAnimations();
        }

        updateMobileMenuItems() {
            const width = window.innerWidth;
            let hasHiddenItems = false;

            const itemsToHide = [
                { breakpoint: this.breakpoints.xl, mainId: 'main-store', mobileId: 'mobile-store' },
                { breakpoint: this.breakpoints.xl, mainId: 'main-theme-toggle', mobileId: 'mobile-theme-toggle' },
                { breakpoint: this.breakpoints.sm, mainId: 'main-search', mobileId: 'mobile-search-container' },
                { breakpoint: this.breakpoints.md, mainId: 'main-notifications', mobileId: 'mobile-notifications' },
                { breakpoint: this.breakpoints.sm, mainId: 'main-cart', mobileId: 'mobile-cart' }
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

            // Close mobile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.toggleButton.contains(e.target) &&
                    !this.mobileMenu.contains(e.target) &&
                    !this.mobileMenu.classList.contains('hidden')) {
                    this.closeMobileMenu();
                }
            });

            // Close mobile menu on window resize to desktop
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1280) {
                    this.closeMobileMenu();
                }
            });

            // Handle escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !this.mobileMenu.classList.contains('hidden')) {
                    this.closeMobileMenu();
                }
            });

            this.setupUserDropdown();
        }

        setupUserDropdown() {
            const userDropdownTrigger = document.getElementById('userHoverTrigger');
            const userDropdownMenu = document.querySelector('[data-dropdown="user"]');

            if (userDropdownTrigger && userDropdownMenu) {
                userDropdownTrigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const isHidden = userDropdownMenu.classList.contains('hidden');

                    // Close other dropdowns
                    document.querySelectorAll('[data-dropdown]').forEach(menu => {
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
                    if (!userDropdownTrigger.contains(e.target) &&
                        !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.add('hidden');
                        userDropdownTrigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        }

        setupAnimations() {
            // Add staggered animation to menu items
            const menuItems = this.mobileMenu.querySelectorAll('.mobile-menu-item');
            menuItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 50}ms`;
            });
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
            // Show menu
            this.mobileMenu.classList.remove('hidden');

            // Remove enter class and add active class for animation
            this.mobileMenu.classList.remove('mobile-menu-enter');
            this.mobileMenu.classList.add('mobile-menu-active');

            // Update button state
            this.toggleButton.setAttribute('aria-expanded', 'true');

            // Switch icons
            this.hamburgerIcon.classList.add('hidden');
            this.closeIcon.classList.remove('hidden');

            // Animate button
            this.toggleButton.style.transform = 'rotate(90deg)';

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        closeMobileMenu() {
            // Add exit animation
            this.mobileMenu.classList.remove('mobile-menu-active');
            this.mobileMenu.classList.add('mobile-menu-exit');

            // Update button state
            this.toggleButton.setAttribute('aria-expanded', 'false');

            // Switch icons
            this.hamburgerIcon.classList.remove('hidden');
            this.closeIcon.classList.add('hidden');

            // Reset button animation
            this.toggleButton.style.transform = 'rotate(0deg)';

            // Restore body scroll
            document.body.style.overflow = '';

            // Hide menu after animation
            setTimeout(() => {
                this.mobileMenu.classList.add('hidden');
                this.mobileMenu.classList.remove('mobile-menu-exit');
                this.mobileMenu.classList.add('mobile-menu-enter');
            }, 300);
        }

        syncSearchInputs() {
            const mainSearchInput = document.querySelector('#main-search input') || document.getElementById('search');
            const mobileSearchInput = document.querySelector('#mobile-search-container input') || document.getElementById('mobile-search');

            if (mainSearchInput && mobileSearchInput) {
                // Sync input values
                mainSearchInput.addEventListener('input', (e) => {
                    mobileSearchInput.value = e.target.value;
                });

                mobileSearchInput.addEventListener('input', (e) => {
                    mainSearchInput.value = e.target.value;
                });

                // Sync form submissions
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

    new EnhancedResponsiveNavbar();

    window.updateCartCount = function() {
        fetch('/api/cart/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const cartBadges = document.querySelectorAll('#main-cart .badge-pulse, #mobile-cart .badge-pulse');
            cartBadges.forEach(badge => {
                badge.textContent = data.count || '0';
                badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
            });
        })
        .catch(error => console.error('Error updating cart count:', error));
    };

    window.updateNotificationCount = function() {
        fetch('/api/notifications/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const notificationBadges = document.querySelectorAll('#main-notifications .badge-pulse, #mobile-notifications .badge-pulse');
            notificationBadges.forEach(badge => {
                badge.textContent = data.count || '0';
                badge.style.display = data.count > 0 ? 'inline-flex' : 'none';
            });
        })
        .catch(error => console.error('Error updating notification count:', error));
    };

    // Auto-update counts periodically
    setInterval(() => {
        if (typeof updateCartCount === 'function') updateCartCount();
        if (typeof updateNotificationCount === 'function') updateNotificationCount();
    }, 30000); // Update every 30 seconds
});
</script>
