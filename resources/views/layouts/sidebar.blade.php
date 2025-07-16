<button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button"
    class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path clip-rule="evenodd" fill-rule="evenodd"
            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
        </path>
    </svg>
</button>

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <x-sidebar.sidebarItem
                href="{{ route('dashboard') }}"
                svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-chart-pie-icon lucide-chart-pie"><path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z"/><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/></svg>'
                label='Dashboard'
            />
            <li>
                <button type="button"
                    class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-products" data-collapse-toggle="dropdown-products">
                    <x-sidebar.dropdownButton 
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-shopping-bag-icon lucide-shopping-bag"><path d="M16 10a4 4 0 0 1-8 0"/><path d="M3.103 6.034h17.794"/><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"/></svg>'
                        value="Product"
                    />
                </button>
                <x-sidebar.dropdownContent 
                    id="dropdown-products"
                    :items="[
                        [
                        'svg' => '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-list-icon lucide-list\'><path d=\'M3 12h.01\'/><path d=\'M3 18h.01\'/><path d=\'M3 6h.01\'/><path d=\'M8 12h13\'/><path d=\'M8 18h13\'/><path d=\'M8 6h13\'/></svg>',
                        'href' => route('products.index'),
                        'value' => 'View All'
                        ],
                        [
                        'svg' => '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-list-plus-icon lucide-list-plus\'><path d=\'M11 12H3\'/><path d=\'M16 6H3\'/><path d=\'M16 18H3\'/><path d=\'M18 9v6\'/><path d=\'M21 12h-6\'/></svg>',
                        'href' => route('products.create'),
                        'value' => 'Add'
                        ],
                    ]"
                />
            </li>
            <li>
                <button type="button"
                    class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-categories" data-collapse-toggle="dropdown-categories">
                    <x-sidebar.dropdownButton 
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-store-icon lucide-store"><path d="M15.536 11.293a1 1 0 0 0 0 1.414l2.376 2.377a1 1 0 0 0 1.414 0l2.377-2.377a1 1 0 0 0 0-1.414l-2.377-2.377a1 1 0 0 0-1.414 0z"/><path d="M2.297 11.293a1 1 0 0 0 0 1.414l2.377 2.377a1 1 0 0 0 1.414 0l2.377-2.377a1 1 0 0 0 0-1.414L6.088 8.916a1 1 0 0 0-1.414 0z"/><path d="M8.916 17.912a1 1 0 0 0 0 1.415l2.377 2.376a1 1 0 0 0 1.414 0l2.377-2.376a1 1 0 0 0 0-1.415l-2.377-2.376a1 1 0 0 0-1.414 0z"/><path d="M8.916 4.674a1 1 0 0 0 0 1.414l2.377 2.376a1 1 0 0 0 1.414 0l2.377-2.376a1 1 0 0 0 0-1.414l-2.377-2.377a1 1 0 0 0-1.414 0z"/></svg>'
                        value="Categories"
                    />
                </button>
                <x-sidebar.dropdownContent 
                    id="dropdown-categories"
                    :items="[
                        [
                        'svg' => '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-list-icon lucide-list\'><path d=\'M3 12h.01\'/><path d=\'M3 18h.01\'/><path d=\'M3 6h.01\'/><path d=\'M8 12h13\'/><path d=\'M8 18h13\'/><path d=\'M8 6h13\'/></svg>',
                        'href' => route('categories.index'),
                        'value' => 'View All'
                        ],
                        [
                        'svg' => '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-list-plus-icon lucide-list-plus\'><path d=\'M11 12H3\'/><path d=\'M16 6H3\'/><path d=\'M16 18H3\'/><path d=\'M18 9v6\'/><path d=\'M21 12h-6\'/></svg>',
                        'href' => route('categories.create'),
                        'value' => 'Add'
                        ],
                    ]"
                />
            </li>
            <li>
                <button type="button"
                    class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-stores" data-collapse-toggle="dropdown-stores">
                    <x-sidebar.dropdownButton 
                        svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-store-icon lucide-store"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>'
                        value="Stores"
                    />
                </button>
                <x-sidebar.dropdownContent 
                    id="dropdown-stores"
                    :items="[
                        [
                        'svg' => '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-list-icon lucide-list\'><path d=\'M3 12h.01\'/><path d=\'M3 18h.01\'/><path d=\'M3 6h.01\'/><path d=\'M8 12h13\'/><path d=\'M8 18h13\'/><path d=\'M8 6h13\'/></svg>',
                        'href' => route('stores.index'),
                        'value' => 'View All'
                        ],
                        [
                        'svg' => '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-list-plus-icon lucide-list-plus\'><path d=\'M11 12H3\'/><path d=\'M16 6H3\'/><path d=\'M16 18H3\'/><path d=\'M18 9v6\'/><path d=\'M21 12h-6\'/></svg>',
                        'href' => route('stores.create'),
                        'value' => 'Add'
                        ],
                    ]"
                />
            </li>
            <x-sidebar.sidebarItem
                href="{{ route('products.index') }}"
                svg='<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 me-2 text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'
                label='User'
            />
        </ul>
    </div>
</aside>
