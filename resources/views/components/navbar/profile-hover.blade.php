{{-- resources/views/components/navbar/profile-hover.blade.php --}}
@props([
    'user' => null,
    'position' => 'right', // 'left' or 'right'
    'triggerId' => 'profileHover'
])

<div id="{{ $triggerId }}" class="absolute {{ $position === 'right' ? 'right-0' : 'left-0' }} mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 dark:bg-gray-800 dark:border-gray-700 z-50 opacity-0 invisible transform translate-y-2 transition-all duration-200 ease-out">
    {{-- Header with user info --}}
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="{{ $user->avatar ?? asset('storage/photos/1/oracle.jpg') }}" 
                 alt="{{ $user->name ?? 'User' }}" 
                 class="w-12 h-12 rounded-full object-cover">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $user->name ?? 'user' }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email ?? 'user@gamil.com' }}</p>
            </div>
        </div>
    </div>

    {{-- Balance Section --}}
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="space-y-3">
            {{-- OVO Balance --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Bebas Ongkir</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Min. belanja Rp0, bebas biaya aplikasi</p>
                    </div>
                </div>
                <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-300 rounded-full">
                    Langganan
                </span>
            </div>

            {{-- Saldo --}}
            {{-- <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-gray-900 dark:text-white">Saldo</span>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Rp0</p>
                    <button class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300">
                        Keluar ↗
                    </button>
                </div>
            </div> --}}
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Quick Actions</h4>
        <div class="grid grid-cols-3 gap-3">
            <button class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-700 dark:text-gray-300">Wishlist</span>
            </button>
            
            <button class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-700 dark:text-gray-300">Favorite Store</span>
            </button>
            
            <button class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-700 dark:text-gray-300">Settings</span>
            </button>
        </div>
    </div>

    {{-- Account Actions --}}
    <div class="p-4">
        <div class="space-y-2">
            {{-- <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Settings
            </a> --}}
            
            <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Help & Support
            </a>

            <button onclick="document.getElementById('logout-form').submit();" class="flex items-center w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>  
</div>

{{-- JavaScript for hover functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('{{ $triggerId }}Trigger');
    const dropdown = document.getElementById('{{ $triggerId }}');
    let hoverTimeout;

    if (trigger && dropdown) {
        // Show on hover
        trigger.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
            dropdown.classList.remove('opacity-0', 'invisible', 'translate-y-2');
            dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
        });

        // Hide when leaving trigger
        trigger.addEventListener('mouseleave', function() {
            hoverTimeout = setTimeout(() => {
                dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
                dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
            }, 150);
        });

        // Keep visible when hovering over dropdown
        dropdown.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
        });

        // Hide when leaving dropdown
        dropdown.addEventListener('mouseleave', function() {
            dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
            dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
        });
    }
});
</script>