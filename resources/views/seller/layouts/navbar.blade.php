
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

            <div id="mobile-theme-toggle" class="hidden mb-3">
                <x-navbar.theme-toggle
                    parentClass="w-full mobile-menu-item flex items-center justify-between px-4 py-3 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white rounded-xl group"
                />
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
            </div>
        </div>
    </nav>
</header>
