<div class="p-6 max-w-3xl">

    {{-- Header + slot usage --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">Nomor WhatsApp</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Paket <span class="font-medium capitalize">{{ $tenant->plan }}</span>
                · {{ $used }} / {{ $max ?? '∞' }} nomor terhubung
            </p>
        </div>
        @if ($canAdd)
            <button wire:click="$set('showAddForm', true)"
                    class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-1.5">
                + Tambah Nomor
            </button>
        @else
            <div class="text-right">
                <span class="text-xs text-slate-400 block mb-1">Batas paket tercapai</span>
                <a href="#" class="text-xs font-medium text-teal-600 hover:underline">Upgrade paket →</a>
            </div>
        @endif
    </div>

    {{-- Flash messages --}}
    @if ($errorMessage)
        <div class="mb-4 flex items-start gap-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3.5 py-3">
            <span class="mt-0.5">⚠️</span>
            <div>
                <p class="font-medium">Terjadi kesalahan</p>
                <p class="text-red-600 mt-0.5">{{ $errorMessage }}</p>
            </div>
            <button wire:click="$set('errorMessage', null)" class="ml-auto text-red-400 hover:text-red-600 shrink-0">✕</button>
        </div>
    @endif

    @if ($successMessage)
        <div class="mb-4 flex items-start gap-2 text-sm text-teal-700 bg-teal-50 border border-teal-200 rounded-lg px-3.5 py-3">
            <span>✓</span>
            <p>{{ $successMessage }}</p>
            <button wire:click="$set('successMessage', null)" class="ml-auto text-teal-400 hover:text-teal-600 shrink-0">✕</button>
        </div>
    @endif

    {{-- Form tambah nomor baru --}}
    @if ($showAddForm)
        <div class="mb-5 bg-white border border-teal-200 rounded-xl p-4">
            <h2 class="text-sm font-semibold mb-3">Tambah Nomor WhatsApp Baru</h2>
            <div class="flex gap-2">
                <input type="text" wire:model="newLabel" placeholder="Label nomor, cth: CS Utama / Sales / Toko Bandung"
                       wire:keydown.enter="addNumber"
                       class="flex-1 rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                <button wire:click="addNumber" wire:loading.attr="disabled" wire:target="addNumber"
                        class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 text-white text-sm font-medium px-4 py-2 rounded-lg inline-flex items-center gap-2">
                    <span wire:loading.remove wire:target="addNumber">Hubungkan</span>
                    <span wire:loading wire:target="addNumber" class="flex items-center gap-1.5">
                        <span class="btn-spinner"></span> Menyiapkan...
                    </span>
                </button>
                <button wire:click="$set('showAddForm', false)" class="text-sm text-slate-500 px-3 rounded-lg border border-slate-200 hover:bg-slate-50">Batal</button>
            </div>
            @error('newLabel')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
            @enderror
            <p class="text-xs text-slate-400 mt-2">Setelah klik Hubungkan, QR Code akan muncul di bawah untuk di-scan dengan WhatsApp di HP kamu.</p>
        </div>
    @endif

    {{-- Panel QR aktif --}}
    @if ($qrSession)
        <div class="mb-5 bg-white border-2 border-teal-400 rounded-xl p-5 text-center"
             wire:poll.3s="refreshQr({{ $qrSession->id }})">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold">Scan QR untuk "{{ $qrSession->label }}"</p>
                <button wire:click="$set('showingQrFor', null)" class="text-xs text-slate-400 hover:text-slate-600">✕ Tutup</button>
            </div>

            @if ($qrSession->qr_code)
                <img src="data:image/png;base64,{{ $qrSession->qr_code }}"
                     class="mx-auto w-52 h-52 border border-slate-200 rounded-lg">
                <p class="text-xs text-slate-400 mt-3">
                    Buka WhatsApp di HP → <strong>Perangkat Tertaut</strong> → <strong>Tautkan Perangkat</strong> → Scan kode ini
                </p>
            @else
                <div class="py-8 flex flex-col items-center gap-2 text-slate-400">
                    <span class="btn-spinner-dark"></span>
                    <p class="text-xs">Menunggu QR dari server...</p>
                    <p class="text-xs max-w-xs text-center">Kalau lebih dari 15 detik, pastikan Node service WhatsApp sudah dijalankan.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Daftar sesi --}}
    @if ($sessions->isEmpty())
        <div class="bg-white border border-slate-200 border-dashed rounded-xl py-12 text-center">
            <p class="text-slate-400 text-sm">Belum ada nomor WhatsApp yang dihubungkan.</p>
            <button wire:click="$set('showAddForm', true)"
                    class="mt-3 text-sm text-teal-600 font-medium hover:underline">
                + Hubungkan nomor pertama kamu
            </button>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($sessions as $sess)
                <div class="bg-white border border-slate-200 rounded-xl p-4
                            {{ $sess->isConnected() ? 'border-l-4 border-l-teal-500' : '' }}
                            {{ $sess->isPending() ? 'border-l-4 border-l-amber-400' : '' }}">
                    <div class="flex items-start gap-3">

                        {{-- Status dot --}}
                        <div class="mt-1 shrink-0">
                            @if ($sess->isConnected())
                                <span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block"></span>
                            @elseif ($sess->isPending())
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block animate-pulse"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-300 inline-block"></span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            {{-- Label editable --}}
                            <div x-data="{ editing: false, label: '{{ $sess->label }}' }">
                                <div x-show="!editing" class="flex items-center gap-1.5">
                                    <p class="font-medium text-sm">{{ $sess->label }}</p>
                                    <button @click="editing = true" class="text-slate-300 hover:text-slate-500 text-xs">✎</button>
                                </div>
                                <div x-show="editing" class="flex items-center gap-1.5" x-cloak>
                                    <input type="text" x-model="label"
                                           class="rounded-md border-slate-300 text-sm py-1 focus:border-teal-500 focus:ring-teal-500"
                                           @keydown.enter="wire.updateLabel({{ $sess->id }}, label); editing = false"
                                           @keydown.escape="editing = false">
                                    <button @click="wire.updateLabel({{ $sess->id }}, label); editing = false"
                                            class="text-xs text-teal-600 font-medium">Simpan</button>
                                    <button @click="editing = false" class="text-xs text-slate-400">Batal</button>
                                </div>
                            </div>

                            <p class="text-xs text-slate-500 mt-0.5">
                                @if ($sess->isConnected())
                                    {{ $sess->phone_number ?? 'tersambung' }}
                                @elseif ($sess->isPending())
                                    Menunggu scan QR...
                                @else
                                    Tidak terhubung
                                @endif
                            </p>
                        </div>

                        {{-- Badge status --}}
                        <span class="text-[11px] px-2 py-0.5 rounded-full font-medium shrink-0
                            {{ $sess->isConnected() ? 'bg-teal-100 text-teal-700' : '' }}
                            {{ $sess->isPending() ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $sess->status === 'disconnected' ? 'bg-slate-100 text-slate-500' : '' }}">
                            {{ $sess->statusLabel() }}
                        </span>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($sess->isConnected())
                                {{-- Toggle AI --}}
                                <button wire:click="toggleAi({{ $sess->id }})"
                                        title="{{ $sess->is_ai_active ? 'Matikan AI untuk nomor ini' : 'Aktifkan AI untuk nomor ini' }}"
                                        class="text-[11px] px-2.5 py-1 rounded-full border transition-colors
                                            {{ $sess->is_ai_active ? 'bg-teal-600 border-teal-600 text-white' : 'border-slate-300 text-slate-500' }}">
                                    AI {{ $sess->is_ai_active ? 'Aktif' : 'Off' }}
                                </button>

                                {{-- Putuskan --}}
                                <button wire:click="disconnect({{ $sess->id }})"
                                        wire:loading.attr="disabled" wire:target="disconnect({{ $sess->id }})"
                                        class="text-xs text-slate-400 hover:text-red-500 disabled:opacity-50 transition-colors">
                                    <span wire:loading.remove wire:target="disconnect({{ $sess->id }})">Putuskan</span>
                                    <span wire:loading wire:target="disconnect({{ $sess->id }})">Memutuskan...</span>
                                </button>

                            @elseif ($sess->isPending())
                                <button wire:click="$set('showingQrFor', {{ $sess->id }})"
                                        class="text-xs text-amber-600 font-medium hover:underline">
                                    Lihat QR
                                </button>

                            @else
                                {{-- Reconnect --}}
                                <button wire:click="connectExisting({{ $sess->id }})"
                                        wire:loading.attr="disabled" wire:target="connectExisting({{ $sess->id }})"
                                        class="text-xs text-teal-600 font-medium hover:underline disabled:opacity-50">
                                    <span wire:loading.remove wire:target="connectExisting({{ $sess->id }})">Hubungkan Ulang</span>
                                    <span wire:loading wire:target="connectExisting({{ $sess->id }})">Menyiapkan QR...</span>
                                </button>
                            @endif

                            {{-- Hapus --}}
                            <button wire:click="delete({{ $sess->id }})"
                                    wire:confirm="Hapus nomor '{{ $sess->label }}'? Sesi akan diputus dan tidak bisa dikembalikan."
                                    wire:loading.attr="disabled" wire:target="delete({{ $sess->id }})"
                                    class="text-xs text-red-400 hover:text-red-600 disabled:opacity-50">
                                <span wire:loading.remove wire:target="delete({{ $sess->id }})">Hapus</span>
                                <span wire:loading wire:target="delete({{ $sess->id }})">Menghapus...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Hint upgrade kalau sudah penuh --}}
    @if (! $canAdd && $sessions->isNotEmpty())
        <div class="mt-4 text-center text-sm text-slate-400">
            Sudah mencapai batas {{ $max }} nomor untuk paket <span class="capitalize">{{ $tenant->plan }}</span>.
            <a href="#" class="text-teal-600 font-medium hover:underline">Upgrade paket</a> untuk menambah lebih banyak nomor.
        </div>
    @endif
</div>
