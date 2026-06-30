<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.3s="refreshStatus">
    <h1 class="text-lg font-semibold mb-4">Koneksi WhatsApp</h1>
    <p class="mt-1 text-sm text-gray-500">
        Paket Anda: <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 uppercase">{{ Auth::user()->tenant->plan }}</span>
        <span class="mx-1">•</span>
        <span class="font-semibold text-gray-700">{{ $sessions->count() }}</span> dari <span
            class="font-semibold text-gray-700">{{ $quotaLimit }}</span> akun terhubung
    </p>

    @if ($sessions->count() < $quotaLimit)
        <div>
            <button wire:click="connect()" wire:loading.attr="disabled"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors duration-150 mt-2 mb-2">
                <span wire:loading.remove wire:target="connect()">+ Tambah Akun WhatsApp</span>
                <span wire:loading wire:target="connect()" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Menyiapkan Sesi...
                </span>
            </button>
        </div>
    @endif

    @if ($errorMessage)
        <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Batas Kuota Tercapai / Gangguan Koneksi</h3>
                    <div class="mt-1 text-sm text-red-700">
                        <p>{{ $errorMessage }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- <div class="bg-white rounded-xl border border-slate-200 p-6 text-center">
        @if (!$session || $session->status === 'disconnected')
            <p class="text-sm text-slate-500 mb-4">Belum terhubung ke WhatsApp.</p>
            <button wire:click="connect" wire:loading.attr="disabled" wire:target="connect"
                class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <span wire:loading.remove wire:target="connect">Hubungkan WhatsApp</span>
                <span wire:loading wire:target="connect" class="flex items-center gap-2">
                    <span class="btn-spinner"></span> Menyiapkan QR...
                </span>
            </button>
        @elseif ($session->status === 'qr_pending')
            <p class="text-sm text-slate-600 mb-3">Scan QR ini dengan WhatsApp di HP Anda:</p>
            @if ($session->qr_code)
                <img src="data:image/png;base64,{{ $session->qr_code }}"
                    class="mx-auto w-56 h-56 border border-slate-200 rounded-lg">
            @else
                <p class="text-xs text-slate-400 inline-flex items-center gap-2 justify-center"><span
                        class="btn-spinner-dark"></span> Menunggu QR dari server...</p>
                <p class="text-xs text-slate-400 mt-3 max-w-xs mx-auto">
                    Kalau ini lebih dari 15 detik, kemungkinan Node service belum jalan
                    atau webhook-nya gagal sampai ke Laravel. Cek terminal Node service kamu.
                </p>
            @endif
            <p class="text-xs text-slate-400 mt-3">Halaman ini akan otomatis update setelah tersambung.</p>
        @elseif ($session->status === 'connected')
            <div class="text-teal-600 text-3xl mb-2">✓</div>
            <p class="text-sm font-medium">Terhubung dengan {{ $session->phone_number }}</p>
            <button wire:click="disconnect" wire:loading.attr="disabled" wire:target="disconnect"
                class="mt-4 text-xs text-red-500 hover:text-red-700 disabled:opacity-60 inline-flex items-center gap-1.5">
                <span wire:loading.remove wire:target="disconnect">Putuskan koneksi</span>
                <span wire:loading wire:target="disconnect">Memutuskan...</span>
            </button>
        @endif
    </div> --}}

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($sessions as $item)
            <div
                class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 flex flex-col hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-4 sm:px-6 bg-gray-50 flex items-center justify-between border-b border-gray-100">
                    <div>
                        @if ($item->status === 'connected')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                Terhubung
                            </span>
                        @elseif ($item->status === 'qr_pending')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                Menunggu Scan
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Terputus
                            </span>
                        @endif
                    </div>
                    <span class="text-xs font-mono text-gray-400 select-all"
                        title="Session ID">{{ $item->session_id }}</span>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between text-center min-h-[250px]">
                    @if ($item->status === 'disconnected')
                        <div class="my-auto">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500 font-medium">Koneksi perangkat terputus atau sesi telah
                                kedaluwarsa.</p>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <button wire:click="connect(null, {{ $item->id }})"
                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Hubungkan Kembali
                            </button>
                            <button wire:click="deleteSession({{ $item->id }})"
                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 hover:text-red-600 hover:border-red-200">
                                Hapus Sesi
                            </button>
                        </div>
                    @elseif ($item->status === 'qr_pending')
                        <div class="my-auto">
                            <p class="text-xs text-gray-500 mb-4 font-medium">Buka WhatsApp > Perangkat Tertaut >
                                Tautkan Perangkat:</p>
                            @if ($item->qr_code)
                                <div
                                    class="inline-block p-3 bg-white rounded-lg shadow-inner border border-gray-150 mx-auto transition-all duration-300">
                                    <img src="data:image/png;base64,{{ $item->qr_code }}" class="w-44 h-44 mx-auto"
                                        alt="WhatsApp QR Code">
                                </div>
                            @else
                                <div class="py-10 text-center text-sm text-gray-400">
                                    <svg class="animate-spin mx-auto h-8 w-8 text-gray-400 mb-3" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Menunggu kode QR dari server...
                                </div>
                            @endif
                        </div>

                        <div class="mt-6">
                            <button wire:click="disconnect({{ $item->id }})"
                                class="text-sm font-medium text-red-600 hover:text-red-500 focus:outline-none focus:underline">
                                Batalkan Proses
                            </button>
                        </div>
                    @elseif ($item->status === 'connected')
                        <div class="my-auto">
                            <div
                                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 tracking-tight">
                                {{ $item->phone_number ?? 'Nomor WhatsApp' }}</h3>
                            <p class="mt-1 text-xs text-gray-500">Nomor aktif & siap merespons chat melalui AI Agen.</p>
                        </div>

                        <div class="mt-6">
                            <button wire:click="disconnect({{ $item->id }})" wire:loading.attr="disabled"
                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-red-200 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                Putuskan Koneksi
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div
                    class="text-center py-16 px-4 bg-white rounded-lg border-2 border-dashed border-gray-300 shadow-sm">
                    <svg class="mx-auto h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-semibold text-gray-900">Belum ada akun WhatsApp terhubung</h3>
                    <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Mulai integrasikan sistem AI chat agen Anda
                        dengan menautkan nomor WhatsApp Anda sekarang.</p>
                    <div class="mt-6">
                        <button wire:click="connect()"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Hubungkan Akun Pertama Anda
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
