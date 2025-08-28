{{-- resources/views/components/voucher-dialog.blade.php --}}
@props([
    'isOpen' => false,
    'vouchers' => [],
    'selectedVoucher' => null,
    'onClose' => 'closeVoucherDialog()',
    'onApply' => 'applyVoucher()',
    'dialogId' => 'voucherDialog'
])

<div 
    id="{{ $dialogId }}" 
    class="fixed inset-0 z-50 overflow-y-auto {{ $isOpen ? '' : 'hidden' }}"
    x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }"
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-xl transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="border-b border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Pilih Voucher Shopee
                    </h3>
                    <button 
                        type="button"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        onclick="{{ $onClose }}"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="flex items-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>Bantuan Tentang Voucher</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex gap-2">
                    <input 
                        type="text"
                        placeholder="Tambah Voucher"
                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                    />
                    <button 
                        type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium"
                    >
                        PAKAI
                    </button>
                </div>
            </div>

            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900 dark:text-white">Voucher Gratis Ongkir</h4>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @if(count($vouchers) > 0)
                        @foreach($vouchers as $voucher)
                            <x-common.voucher-card 
                                :voucher="$voucher"
                                :isSelected="$selectedVoucher && $selectedVoucher['id'] === $voucher['id']"
                                :onClick="'selectVoucher(' . json_encode($voucher) . ')'"
                            />
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm">Tidak ada voucher tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 p-4 flex gap-3">
                <button 
                    type="button"
                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 font-medium"
                    onclick="{{ $onClose }}"
                >
                    NANTI SAJA
                </button>
                <button 
                    type="button"
                    class="flex-1 px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-md font-medium"
                    onclick="{{ $onApply }}"
                >
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function selectVoucher(voucher) {
    console.log('Selected voucher:', voucher);
    
    if (typeof window.voucherSelectionHandler === 'function') {
        window.voucherSelectionHandler(voucher);
    }
}

function closeVoucherDialog() {
    const dialog = document.getElementById('{{ $dialogId }}');
    if (dialog) {
        dialog.classList.add('hidden');
    }
}

function applyVoucher() {
    if (typeof window.applyVoucherHandler === 'function') {
        window.applyVoucherHandler();
    }
    closeVoucherDialog();
}

function openVoucherDialog() {
    const dialog = document.getElementById('{{ $dialogId }}');
    if (dialog) {
        dialog.classList.remove('hidden');
    }
}
</script>