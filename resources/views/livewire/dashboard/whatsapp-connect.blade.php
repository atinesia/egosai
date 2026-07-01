<div class="p-6 max-w-lg" wire:poll.3s="refreshStatus">
    <h1 class="text-lg font-semibold mb-4">Koneksi WhatsApp</h1>

    @if ($errorMessage)
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3.5 py-3">
            <p class="font-medium mb-0.5">Gagal terhubung ke service WhatsApp</p>
            <p class="text-red-600">{{ $errorMessage }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6 text-center">
        @if (! $session || $session->status === 'disconnected')
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
                <img src="data:image/png;base64,{{ $session->qr_code }}" class="mx-auto w-56 h-56 border border-slate-200 rounded-lg">
            @else
                <p class="text-xs text-slate-400 inline-flex items-center gap-2 justify-center"><span class="btn-spinner-dark"></span> Menunggu QR dari server...</p>
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
    </div>
</div>
