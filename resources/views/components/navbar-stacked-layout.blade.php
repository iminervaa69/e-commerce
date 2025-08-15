<header>
    <nav class="fixed z-30 w-full bg-white border-b border-gray-200 py-3 px-4">
        <div class="flex justify-between items-center max-w-screen-2xl mx-auto">
            <div class="flex justify-start items-center">
                <!-- Desktop menu -->
                <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1">
                    <ul class="flex flex-col mt-4 space-x-6 text-sm font-medium lg:flex-row xl:space-x-8 lg:mt-0">
                        <li>
                        </li>
                        <li>
                            <button id="dropdownNavbarLink"
                                    data-dropdown-toggle="dropdownNavbar"
                                    class="flex justify-between items-center py-2 pr-4 pl-3 w-full font-medium text-gray-700 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-primary-700 md:p-0 md:w-auto md:dark:hover:bg-transparent">
                                <svg class="ml-1 w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <div id="dropdownNavbar" class="hidden z-20 w-44 font-normal bg-white rounded divide-y divide-gray-100 shadow">
                                <ul class="py-1 text-sm text-gray-700" aria-labelledby="dropdownLargeButton">
                                    <li>
                                        <a href="#" class="block py-2 px-4 hover:bg-gray-100">
                                        </a>
                                    </li>
                                    <!-- Add more dropdown items as needed -->
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-between items-center lg:order-2">
                <!-- GitHub Star Button (optional) -->
                @if(config('app.github_repo'))
                <div class="mr-3 -mb-1 hidden sm:block">
                </div>
                @endif

                <!-- Notifications -->
                <button type="button"
                        data-dropdown-toggle="notification-dropdown"
                        class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-300">
                    <span class="sr-only">{{ __('View notifications') }}</span>
                    <!-- Bell icon -->
                    <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                </button>

                <!-- Notifications Dropdown -->
                <div class="hidden overflow-hidden z-50 my-4 max-w-sm text-base list-none bg-white rounded divide-y divide-gray-100 shadow-lg" id="notification-dropdown">
                    <div class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50">
                        {{ __('Notifications') }}
                    </div>
                    <div>
                        @forelse($notifications ?? [] as $notification)
                        <a href="{{ $notification->url ?? '#' }}" class="flex py-3 px-4 border-b hover:bg-gray-100">
                            <div class="flex-shrink-0">
                                <img class="w-11 h-11 rounded-full"
                                     src="{{ $notification->avatar ?? asset('images/default-avatar.png') }}"
                                     alt="{{ $notification->user_name ?? 'User' }} avatar">
                                @if($notification->is_new ?? false)
                                <div class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 rounded-full border border-white bg-primary-700">
                                    <svg aria-hidden="true" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z"></path>
                                        <path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="pl-3 w-full">
                                <div class="text-gray-500 font-normal text-sm mb-1.5">
                                    {{ $notification->message }}
                                </div>
                                <div class="text-xs font-medium text-primary-700">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="py-4 px-4 text-center text-gray-500">
                            {{ __('No notifications') }}
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Theme Toggle -->
                <button id="theme-toggle"
                        data-tooltip-target="tooltip-toggle"
                        type="button"
                        class="text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 rounded-lg text-sm p-2.5">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div id="tooltip-toggle" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip">
                    {{ __('Toggle dark mode') }}
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>

                <!-- Apps -->
                <button type="button"
                        data-dropdown-toggle="apps-dropdown"
                        class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-300">
                    <span class="sr-only">{{ __('View apps') }}</span>
                    <!-- Apps icon -->
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>

                <!-- Apps Dropdown -->
                <div class="hidden overflow-hidden z-50 my-4 max-w-sm text-base list-none bg-white rounded divide-y divide-gray-100 shadow-lg" id="apps-dropdown">
                    <div class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50">
                        {{ __('Apps') }}
                    </div>
                    <div class="grid grid-cols-3 gap-4 p-4">
                        <!-- Add more app links as needed -->
                    </div>
                </div>

                <!-- User Menu -->
                @auth
                <button type="button"
                        class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 flex-shrink-0 focus:ring-4 focus:ring-gray-300"
                        id="userMenuDropdownButton"
                        aria-expanded="false"
                        data-dropdown-toggle="userMenuDropdown">
                    <span class="sr-only">{{ __('Open user menu') }}</span>
                    <img class="w-8 h-8 rounded-full"
                         src="{{ Auth::user()->avatar ?? asset('images/default-avatar.jpg') }}"
                         alt="{{ Auth::user()->name }}">
                </button>

                <!-- User Dropdown menu -->
                <div class="hidden z-50 my-4 w-56 text-base list-none bg-white rounded divide-y divide-gray-100 shadow" id="userMenuDropdown">
                    <div class="py-3 px-4">
                        <span class="block text-sm font-semibold text-gray-900">
                            {{ Auth::user()->name }}
                        </span>
                        <span class="block text-sm font-light text-gray-500 truncate">
                            {{ Auth::user()->email }}
                        </span>
                    </div>
                    <ul class="py-1 font-light text-gray-500" aria-labelledby="userMenuDropdownButton">
                        <li>
                            <a href="{{ route('profile.show') }}" class="flex items-center py-2 px-4 text-sm hover:bg-gray-100">
                                <svg class="mr-2 w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('My Profile') }}
                            </a>
                        </li>
                    </ul>
                    <ul class="py-1 font-light text-gray-500" aria-labelledby="dropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left py-2 px-4 text-sm hover:bg-gray-100">
                                    {{ __('Sign out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <div class="flex space-x-2">
                </div>
                @endauth

                <!-- Mobile menu button -->
                <button type="button"
                        id="toggleMobileMenuButton"
                        data-collapse-toggle="toggleMobileMenu"
                        class="items-center p-2 text-gray-500 rounded-lg md:ml-2 lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:ring-4 focus:ring-gray-300">
                    <span class="sr-only">{{ __('Open menu') }}</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile menu -->
    <nav class="bg-white">
        <ul id="toggleMobileMenu" class="hidden flex-col mt-0 pt-16 w-full text-sm font-medium lg:hidden">
            <li class="block border-b">
            </li>
            <li class="block border-b">
                <button type="button"
                        data-collapse-toggle="dropdownMobileNavbar"
                        class="flex justify-between items-center py-3 px-4 w-full text-gray-900 lg:py-0 lg:hover:underline lg:px-0">
                    {{ __('Dropdown') }}
                    <svg class="w-6 h-6 text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <ul id="dropdownMobileNavbar" class="hidden">
                    <li class="block border-t border-b">
                        <a href="#" class="block py-3 px-4 text-gray-900 lg:py-0 lg:hover:underline lg:px-0">
                            {{ __('Item 1') }}
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
