@props([
    'tabs' => ['Detail', 'Spesifikasi', 'Info Penting'],
    'activeTab' => 'Detail',
    'details' => '',
    'specifications' => [],
    'importantInfo' => ''
])

<div class="mt-8">
    {{-- Tab Headers --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            @foreach($tabs as $tab)
                <button 
                    class="py-2 px-1 border-b-2 font-medium text-sm tab-button {{ $tab === $activeTab ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    onclick="switchTab('{{ strtolower($tab) }}')"
                    data-tab="{{ strtolower($tab) }}"
                >
                    {{ $tab }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        <div id="detail-tab" class="tab-content {{ strtolower($activeTab) !== 'detail' ? 'hidden' : '' }}">
            {{-- <div class="pt-4 pb-2 space-y-1">
                @isset($condition)
                    <div class="flex justify-between">
                        <span class="text-gray-200">Kondisi:</span>
                        <span class="font-medium dark:text-white">{{ $condition }}</span>
                    </div>  
                @endisset
                @isset($minOrder)
                <div class="flex justify-between">
                    <span class="text-gray-200">Min. Pemesanan:</span>
                    <span class="font-medium dark:text-white">{{ $minOrder }} Buah</span>
                </div>
                @endisset()
                @isset($preorderTime)
                <div class="flex justify-between">
                    <span class="text-gray-200">Waktu Preorder:</span>
                    <span class="font-medium dark:text-white">{{ $preorderTime }} Hari</span>
                </div>
                @endisset()
                @if(count($tags) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-200">Etalase:</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($tags as $tag)
                                <span class="px-2 py-1 border border-cyan-800 text-cyan-800 text-xs rounded">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div> --}}
            @if($details)
                <div class="prose max-w-none text-gray-600 dark:text-gray-200">
                    {!! $details !!}
                </div>
            @else
                <p class="text-gray-600 dark:text-white">{{ $details ?: 'Detail produk tidak tersedia' }}</p>
            @endif
        </div>

        <div id="spesifikasi-tab" class="tab-content {{ strtolower($activeTab) !== 'spesifikasi' ? 'hidden' : '' }}">
            @if(count($specifications) > 0)
                <div class="space-y-4">
                    @foreach($specifications as $category => $specs)
                        <div class="dark:bg-gray-800 border border-dashed border-gray-500 dark:border-gray-700 rounded-md p-2 dark:text-gray-700">
                            <h3 class="font-semibold text-lg mb-2 dark:text-white">{{ $category }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($specs as $spec => $value)
                                    <div class="flex justify-between py-2 border-b border-gray-400 dark:border-gray-600">
                                        <span class="text-gray-900 dark:text-gray-200">{{ $spec }}</span>
                                        <span class="font-medium dark:text-gray-100">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600">Spesifikasi produk akan ditampilkan di sini.</p>
            @endif
        </div>

        <div id="info penting-tab" class="tab-content {{ strtolower($activeTab) !== 'info penting' ? 'hidden' : '' }}">
            @if($importantInfo)
                <div class="prose max-w-none">
                    {!! $importantInfo !!}
                </div>
            @else
                <div class="space-y-4">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Pastikan untuk memeriksa kompatibilitas produk dengan sistem Anda sebelum membeli.
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">Informasi penting lainnya akan ditampilkan di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-cyan-500', 'text-cyan-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    const selectedContent = document.getElementById(tabName + '-tab');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    const selectedButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (selectedButton) {
        selectedButton.classList.remove('border-transparent', 'text-gray-500');
        selectedButton.classList.add('border-cyan-500', 'text-cyan-600');
    }
}
</script>