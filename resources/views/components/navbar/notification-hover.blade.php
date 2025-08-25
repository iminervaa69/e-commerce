{{-- resources/views/components/navbar/notification-hover.blade.php --}}
@props([
    'position' => 'right', // 'left', 'right', or 'center'
    'triggerId' => 'notificationHover'
])

<div id="{{ $triggerId }}" class="absolute 
    @if($position === 'right') right-0 
    @elseif($position === 'left') left-0 
    @else left-1/2 transform -translate-x-1/2 
    @endif 
    mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 dark:bg-gray-800 dark:border-gray-700 z-50 opacity-0 invisible translate-y-2 transition-all duration-200 ease-out">
    <!-- Header -->
    <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-gray-600">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
        <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>
    
    <!-- Tabs -->
    <div class="flex border-b border-gray-200 dark:border-gray-600">
        <button id="{{ $triggerId }}_tab_transaksi" class="flex-1 py-2 px-4 text-sm font-medium text-green-600 border-b-2 border-green-600 bg-green-50 dark:bg-gray-800 dark:text-green-400 transition-all">
            Transaksi
        </button>
        <button id="{{ $triggerId }}_tab_update" class="flex-1 py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
            Update
        </button>
    </div>
    
    <!-- Content Container -->
    <div class="max-h-96 overflow-y-auto">
        <!-- Transaksi Tab Content -->
        <div id="{{ $triggerId }}_content_transaksi" class="p-4">
            <!-- Purchase Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Pembelian</h4>
                    <a href="#" class="text-xs text-green-600 hover:text-green-700 dark:text-green-400 transition-colors">Lihat Semua</a>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Menunggu Pembayaran</p>
                
                <!-- Status Icons -->
                <div class="flex justify-between mb-4">
                    <div class="text-center flex-1">
                        <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Menunggu Konfirmasi</span>
                    </div>
                    <div class="text-center flex-1">
                        <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Pesanan Diproses</span>
                    </div>
                    <div class="text-center flex-1">
                        <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C2.89,4 20,4.89 20,4Z"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Sedang Dikirim</span>
                    </div>
                    <div class="text-center flex-1">
                        <div class="w-8 h-8 mx-auto mb-1 text-green-600 dark:text-green-400">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Sampai Tujuan</span>
                    </div>
                </div>
            </div>
            
            <!-- Sales Section -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Penjualan</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Cek pesanan yang masuk dan perkembangan tokomu secara rutin di satu tempat!</p>
                <button class="w-full py-2 px-4 text-sm font-medium text-green-600 border border-green-600 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 dark:text-green-400 dark:border-green-400 transition-colors">
                    Masuk ke RhodeShop Seller
                </button>
            </div>
            
            <!-- For You Section -->
            <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Untuk Kamu</h4>
                <div class="bg-gray-100 dark:bg-gray-600 rounded-lg p-3 mb-3">
                    <div class="w-full h-20 bg-gray-200 dark:bg-gray-500 rounded mb-2 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM6 6v14h12V6H6zm3-2V2h6v2H9zm2 4a1 1 0 112 0v8a1 1 0 11-2 0V8zm4 0a1 1 0 112 0v8a1 1 0 11-2 0V8z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Tidak ada notifikasi terbaru</p>
                </div>
                <div class="flex justify-between text-xs">
                    <button class="text-green-600 hover:text-green-700 dark:text-green-400 transition-colors">Tandai semua dibaca</button>
                    <a href="#" class="text-green-600 hover:text-green-700 dark:text-green-400 transition-colors">Lihat selengkapnya</a>
                </div>
            </div>
        </div>

        <!-- Update Tab Content (Hidden by default) -->
        <div id="{{ $triggerId }}_content_update" class="p-4 hidden">
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 00-15 0v5h5l-5 5-5-5h5v-5a12.5 12.5 0 0125 0v5z"/>
                </svg>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Tidak Ada Update</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Update aplikasi dan fitur terbaru akan muncul di sini</p>
                <button class="text-sm text-green-600 hover:text-green-700 dark:text-green-400 transition-colors">
                    Periksa Update
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for hover and tab functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('{{ $triggerId }}Trigger');
    const dropdown = document.getElementById('{{ $triggerId }}');
    const transaksiTab = document.getElementById('{{ $triggerId }}_tab_transaksi');
    const updateTab = document.getElementById('{{ $triggerId }}_tab_update');
    const transaksiContent = document.getElementById('{{ $triggerId }}_content_transaksi');
    const updateContent = document.getElementById('{{ $triggerId }}_content_update');
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

    // Tab functionality
    if (transaksiTab && updateTab && transaksiContent && updateContent) {
        // Transaksi tab click
        transaksiTab.addEventListener('click', function() {
            // Update tab styles
            transaksiTab.classList.add('text-green-600', 'border-b-2', 'border-green-600', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400');
            transaksiTab.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            
            updateTab.classList.remove('text-green-600', 'border-b-2', 'border-green-600', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400');
            updateTab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            
            // Show/hide content
            transaksiContent.classList.remove('hidden');
            updateContent.classList.add('hidden');
        });

        // Update tab click
        updateTab.addEventListener('click', function() {
            // Update tab styles
            updateTab.classList.add('text-green-600', 'border-b-2', 'border-green-600', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400');
            updateTab.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            
            transaksiTab.classList.remove('text-green-600', 'border-b-2', 'border-green-600', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400');
            transaksiTab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            
            // Show/hide content
            updateContent.classList.remove('hidden');
            transaksiContent.classList.add('hidden');
        });
    }
});
</script>