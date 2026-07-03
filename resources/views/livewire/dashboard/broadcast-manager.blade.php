<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.5s>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-5 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">WhatsApp Broadcast</h1>
            <p class="mt-1 text-sm text-gray-500">Kirim pesan massal secara teratur dan aman menggunakan fitur Smart
                Rotator multi-device.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button wire:click="openModal"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 transition">
                + Buat Broadcast Baru
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6 text-sm font-medium text-green-800">
            {{ session('success') }}
        </div>
    @elseif (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6 text-sm font-medium text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @forelse($broadcasts as $item)
            <div
                class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-bold text-gray-900 truncate">{{ $item->name }}</h3>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $item->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $item->status === 'processing' ? 'bg-blue-100 text-blue-800 animate-pulse' : '' }}
                            {{ $item->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $item->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $item->status }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Dibuat pada {{ $item->created_at->format('d M Y H:i') }}</p>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2 italic">"{{ $item->message }}"</p>
                </div>

                <div class="w-full md:w-64 flex-shrink-0">
                    <div class="flex justify-between text-xs font-medium text-gray-500 mb-1">
                        <span>Progress Kirim</span>
                        <span>{{ $item->sent_count + $item->failed_count }} / {{ $item->total_contacts }} Pesan</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $percent =
                                $item->total_contacts > 0
                                    ? (($item->sent_count + $item->failed_count) / $item->total_contacts) * 100
                                    : 0;
                        @endphp
                        <div class="bg-teal-600 h-2 rounded-full transition-all duration-500"
                            style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="flex justify-between text-xxs mt-1 text-gray-400">
                        <span class="text-green-600">✓ {{ $item->sent_count }} Sukses</span>
                        <span class="text-red-500">✕ {{ $item->failed_count }} Gagal</span>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="text-center py-12 bg-white rounded-lg border-2 border-dashed border-gray-300 text-sm text-gray-500">
                Belum ada kampanye broadcast yang dibuat.
            </div>
        @endforelse
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-0" role="dialog"
            aria-modal="true" aria-labelledby="modal-title">

            <div class="fixed inset-0 bg-slate-900/50 transition-opacity duration-300 ease-out"
                wire:click="$set('isModalOpen', false)" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform-gpu transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 relative z-10 duration-300">
                <form wire:submit.prevent="sendBroadcast">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Buat Kampanye Blast Baru</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700">Nama Kampanye /
                                    Keterangan</label>
                                <input type="text" wire:model="name"
                                    class="mt-1.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg shadow-none"
                                    placeholder="Contoh: Promo Diskon Akhir Bulan" autocomplete="off">
                                @error('name')
                                    <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700">Target Penerima
                                    Broadcast</label>
                                @if (auth()->user()->tenant->plan === 'starter' || auth()->user()->tenant->plan === 'trial')
                                    <select disabled
                                        class="mt-1.5 bg-gray-50 border-gray-300 text-gray-500 block w-full sm:text-sm rounded-lg">
                                        <option>Kirim ke Semua Kontak (Maks 1 Device)</option>
                                    </select>
                                    <span class="text-xxs text-amber-600 mt-1 block">💡 Upgrade ke paket **Pro** untuk
                                        menikmati fitur *Smart Rotator* Multi-Device agar broadcast lebih aman.</span>
                                @else
                                    <select wire:model.live="targetType"
                                        class="mt-1.5 focus:ring-1 focus:ring-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                                        <option value="all">Kirim ke Semua Kontak (Smart Rotator Multi-Device)
                                        </option>
                                        <option value="device">Filter Berdasarkan Nomor WhatsApp Tertentu</option>
                                    </select>
                                @endif
                            </div>

                            @if ($targetType === 'device')
                                <div x-transition>
                                    <label class="block text-sm font-semibold text-gray-700">Pilih Nomor WhatsApp
                                        Pengirim</label>
                                    <select wire:model="selectedSessionId"
                                        class="mt-1.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg shadow-none">
                                        <option value="">-- Pilih Nomor Pengirim --</option>
                                        @foreach ($devices as $device)
                                            <option value="{{ $device->id }}">{{ $device->name }}
                                                ({{ $device->phone_number ?? $device->session_id }})
                                                -
                                                {{ $device->status }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedSessionId')
                                        <span
                                            class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-semibold text-gray-700">Isi Pesan WhatsApp</label>
                                <textarea wire:model="message" rows="6"
                                    class="mt-1.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg text-gray-800 shadow-none"
                                    placeholder="Tulis teks promosi/informasi blast Anda di sini..."></textarea>
                                @error('message')
                                    <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-2 border-t border-slate-100 mt-4">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-sm font-semibold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-2 sm:w-auto transition-colors">
                            Mulai Kirim Blast
                        </button>
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:w-auto transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
