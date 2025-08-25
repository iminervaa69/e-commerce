{{-- Updated header.blade.php with notification hover integration --}}
<header>
    <nav class="fixed z-100 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 py-3 px-4">
        <div class="flex justify-between items-center max-w-screen-2xl mx-auto">
            <div class="flex justify-start items-center w-2/3">
                <a class="flex">
                    <img src="{{ asset('storage/photos/1/icon.png') }}" class="h-8 me-3" alt="FlowBite Logo" />
                    <span class="self-center hidden sm:flex text-2xl font-semibold whitespace-nowrap text-gray-900 dark:text-white transition-colors duration-300">RhodeShop</span>
                </a>
                <x-form.search 
                    containerClass='ms-10 w-[700px]' 
                    id='search'
                    placeholder="Search..."
                    inputClass=''
                />
            </div>
            <div class="flex justify-between items-center w-1/3 lg:order-2">
                <!-- Cart Button -->
                <div>
                    <x-button.iconButton
                        type="button"
                        label="Apps"
                        href="{{ route('cart.index') }}"
                        id="cartButton"
                        parentClass="ms-2"
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>'
                        />
                </div>

                <!-- Notification Button with Hover -->
                <div class="relative ms-2">
                    <button type="button" 
                            id="notificationHoverTrigger"
                            class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-colors" 
                            aria-expanded="false">
                        <span class="sr-only">Open notifications</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0"/>
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>
                        </svg>
                        {{-- Notification Badge (optional) --}}
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">3</span>
                    </button>
                    
                    {{-- Notification Hover Component --}}
                    <x-navbar.notification-hover
                        position="center"
                        triggerId="notificationHover"
                    />
                </div>

                <!-- Theme Toggle -->
                <x-navbar.theme-toggle 
                    parentClass="ms-2" 
                />

                <!-- Store Menu with Hover -->
                <div class="relative ms-10">
                    <button type="button" 
                            id="storeHoverTrigger"
                            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" 
                            aria-expanded="false">
                        <span class="sr-only">Store</span>
                        <img class="w-8 h-8 rounded-full object-cover" 
                             src="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}" 
                             alt="Store profile">
                    </button>
                    
                    {{-- Store Profile Hover Component --}}
                    {{-- <x-navbar.profile-hover 
                        :user="Auth::user()" 
                        position="right" 
                        triggerId="storeHover"
                    /> --}}
                </div>

                <!-- User Menu with Hover -->
                <div class="relative ms-10">
                    <button type="button" 
                            id="userHoverTrigger"
                            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" 
                            aria-expanded="false">
                        <span class="sr-only">User</span>
                        <img class="w-8 h-8 rounded-full object-cover" 
                             src="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}" 
                             alt="User profile">
                    </button>
                    
                    {{-- User Profile Hover Component --}}
                    <x-navbar.profile-hover 
                        :user="Auth::user()" 
                        position="right" 
                        triggerId="userHover"
                    />
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button type="button" id="toggleMobileMenuButton" data-collapse-toggle="toggleMobileMenu" class="items-center p-2 text-gray-500 rounded-lg md:ml-2 lg:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                    <span class="sr-only">Open menu</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu -->
    <nav class="bg-white dark:bg-gray-900">
        <ul id="toggleMobileMenu" class="hidden flex-col mt-0 pt-16 w-full text-sm font-medium lg:hidden">
            <li class="block border-b dark:border-gray-700">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="default-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search..." required>
                </div>
            </li>
        </ul>
    </nav>
</header>