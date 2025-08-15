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
                <!-- Notifications -->
                <div>
                    <x-button.iconButton
                        type="button"
                        label="Notifications"
                        data="notification-dropdown"
                        id="notification-dropdown-btn"
                        parentClass="ms-2"
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>'
                    /> 
                </div>
                <!-- Notifications Dropdown menu -->
                <div class="hidden overflow-hidden z-50 my-4 w-80 text-base list-none bg-white rounded-lg shadow-lg dark:bg-gray-700" id="notification-dropdown">
                    <!-- Header -->
                    <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                        <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200 dark:border-gray-600">
                        <button class="flex-1 py-2 px-4 text-sm font-medium text-green-600 border-b-2 border-green-600 bg-green-50 dark:bg-gray-800 dark:text-green-400">
                            Transaksi
                        </button>
                        <button class="flex-1 py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Update
                        </button>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4">
                        <!-- Purchase Section -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Pembelian</h4>
                                <a href="#" class="text-xs text-green-600 hover:text-green-700 dark:text-green-400">Lihat Semua</a>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Menunggu Pembayaran</p>
                            
                            <!-- Status Icons -->
                            <div class="flex justify-between mb-4">
                                <div class="text-center">
                                    <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Menunggu Konfirmasi</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Pesanan Diproses</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C2.89,4 20,4.89 20,4Z"/></svg>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Sedang Dikirim</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Sampai Tujuan</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sales Section -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Penjualan</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Cek pesanan yang masuk dan perkembangan tokomu secara rutin di satu tempat!</p>
                            <button class="w-full py-2 px-4 text-sm font-medium text-green-600 border border-green-600 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 dark:text-green-400 dark:border-green-400">
                                Masuk ke Tokopedia Seller
                            </button>
                        </div>
                        
                        <!-- For You Section -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Untuk Kamu</h4>
                            <div class="bg-gray-100 dark:bg-gray-600 rounded-lg p-3 mb-3">
                                <div class="w-full h-20 bg-gray-200 dark:bg-gray-500 rounded mb-2"></div>
                            </div>
                            <div class="flex justify-between text-xs">
                                <a href="#" class="text-green-600 hover:text-green-700 dark:text-green-400">Tandai semua dibaca</a>
                                <a href="#" class="text-green-600 hover:text-green-700 dark:text-green-400">Lihat selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apps -->
                <div>
                    <x-button.iconButton
                        type="button"
                        label="Apps"
                        data="apps-dropdown"
                        id="apps-dropdown-btn"
                        parentClass="ms-2"
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-blocks-icon lucide-blocks"><path d="M10 22V7a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5a1 1 0 0 0-1-1H2"/><rect x="14" y="2" width="8" height="8" rx="1"/></svg>'
                    /> 
                </div>

                <!-- Theme Toggle -->
                <x-navbar.theme-toggle 
                    parentClass="ms-2" 
                />

                <!-- Apps Dropdown menu -->
                <div class="hidden overflow-hidden z-50 my-4 max-w-sm text-base list-none bg-white rounded divide-y divide-gray-100 shadow-lg dark:bg-gray-700 dark:divide-gray-600" id="apps-dropdown">
                    <div class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        Apps
                    </div>
                    <div class="grid grid-cols-3 gap-4 p-4">
                        <a href="#" class="block p-4 text-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 group">
                            <svg aria-hidden="true" class="mx-auto mb-1 w-7 h-7 text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                            <div class="text-sm text-gray-900 dark:text-white">Sales</div>
                        </a>
                    </div>
                </div>

                <x-button.profilPicture
                    type="button"
                    label="Store"
                    id="storeMenuDropdownButton"
                    {{-- data="storeMenuDropdown" --}}
                    parentClass="ms-10"
                    imageURL="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                />

                <!-- User Menu -->
                <x-button.profilPicture
                    type="button"
                    label="User"
                    id="userMenuDropdownButton"
                    {{-- data="userMenuDropdown" --}}
                    parentClass="ms-10"
                    imageURL="{{ Auth::user()->avatar ?? asset('storage/photos/1/oracle.jpg') }}"
                />
                
                <!-- Mobile Menu Toggle -->
                <button type="button" id="toggleMobileMenuButton" data-collapse-toggle="toggleMobileMenu" class="items-center p-2 text-gray-500 rounded-lg md:ml-2 lg:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                    <span class="sr-only">Open menu</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>
    </nav>
    <nav class="bg-white dark:bg-gray-900">
        <!-- Mobile menu -->
        <ul id="toggleMobileMenu" class="hidden flex-col mt-0 pt-16 w-full text-sm font-medium lg:hidden">
            <li class="block border-b dark:border-gray-700">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="default-search" class="block w- p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search..." required>
                </div>
            </li>
        </ul>
    </nav>
</header>