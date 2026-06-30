<div>
    {{-- Progress steps --}}
    <div class="flex items-center justify-between mb-8">
        @php
            $labels = ['Tentang Bisnis', 'Kepribadian AI', 'Knowledge Base', 'Hubungkan WhatsApp'];
        @endphp
        @foreach ($labels as $i => $label)
            @php $n = $i + 1; @endphp
            <div class="flex-1 flex items-center">
                <button
                    wire:click="goToStep({{ $n }})"
                    @if($n >= $step) disabled @endif
                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium shrink-0
                        {{ $n < $step ? 'bg-teal-600 text-white' : ($n === $step ? 'bg-teal-600 text-white ring-4 ring-teal-100' : 'bg-slate-200 text-slate-500') }}">
                    {{ $n < $step ? '✓' : $n }}
                </button>
                <span class="text-xs ml-2 hidden sm:inline {{ $n === $step ? 'text-slate-900 font-medium' : 'text-slate-400' }}">{{ $label }}</span>
                @if (! $loop->last)
                    <div class="flex-1 h-px bg-slate-200 mx-2"></div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">

        {{-- STEP 1: Tentang Bisnis --}}
        @if ($step === 1)
            <h2 class="text-lg font-semibold mb-1">Ceritakan tentang bisnis kamu</h2>
            <p class="text-sm text-slate-500 mb-5">Informasi ini akan dipakai AI untuk memahami konteks saat membalas pelanggan.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Bisnis</label>
                    <input type="text" wire:model="business_name" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                    @error('business_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Bisnis</label>
                    <select wire:model="business_category" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                        <option value="retail">Retail / Toko</option>
                        <option value="fashion">Fashion</option>
                        <option value="fnb">Makanan & Minuman</option>
                        <option value="jasa">Jasa</option>
                        <option value="edukasi">Edukasi / Kursus</option>
                        <option value="teknologi">Teknologi / SaaS</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi singkat bisnis</label>
                    <textarea wire:model="description" rows="3" placeholder="Contoh: Kami jual sepatu lokal handmade, target anak muda, gratis ongkir se-Jabodetabek."
                              class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500"></textarea>
                </div>
            </div>
        @endif

        {{-- STEP 2: Kepribadian AI --}}
        @if ($step === 2)
            <h2 class="text-lg font-semibold mb-1">Atur kepribadian AI</h2>
            <p class="text-sm text-slate-500 mb-5">AI akan membalas pelanggan sesuai gaya bicara yang kamu pilih di sini.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Gaya Bicara</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border rounded-lg p-3 cursor-pointer text-sm {{ $tone === 'friendly' ? 'border-teal-500 ring-1 ring-teal-500 bg-teal-50' : 'border-slate-200' }}">
                            <input type="radio" wire:model.live="tone" value="friendly" class="hidden">
                            <span class="font-medium block">😊 Ramah & Santai</span>
                            <span class="text-xs text-slate-500">Seperti ngobrol dengan teman</span>
                        </label>
                        <label class="border rounded-lg p-3 cursor-pointer text-sm {{ $tone === 'formal' ? 'border-teal-500 ring-1 ring-teal-500 bg-teal-50' : 'border-slate-200' }}">
                            <input type="radio" wire:model.live="tone" value="formal" class="hidden">
                            <span class="font-medium block">🤝 Profesional & Formal</span>
                            <span class="text-xs text-slate-500">Bahasa baku, sopan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Prompt AI (bisa diedit bebas)</label>
                    <textarea wire:model="system_prompt" rows="5" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                    @error('system_prompt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pesan saat AI tidak tahu jawabannya</label>
                    <input type="text" wire:model="greeting_message" placeholder="Mohon tunggu, tim kami akan segera membantu Anda."
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
            </div>
        @endif

        {{-- STEP 3: Knowledge Base awal --}}
        @if ($step === 3)
            <h2 class="text-lg font-semibold mb-1">Tambahkan info dasar (opsional)</h2>
            <p class="text-sm text-slate-500 mb-5">Isi FAQ singkat supaya AI langsung bisa menjawab. Bisa dilewati dan ditambah nanti.</p>

            <div class="space-y-4">
                @foreach ($faqs as $i => $faq)
                    <div class="border border-slate-200 rounded-lg p-3 relative">
                        @if (count($faqs) > 1)
                            <button wire:click="removeFaqRow({{ $i }})" type="button" class="absolute top-2 right-2 text-xs text-slate-400 hover:text-red-500">✕</button>
                        @endif
                        <input type="text" wire:model="faqs.{{ $i }}.title" placeholder="Pertanyaan, contoh: Berapa lama pengiriman?"
                               class="w-full rounded-lg border-slate-300 text-sm mb-2 focus:border-teal-500 focus:ring-teal-500">
                        <textarea wire:model="faqs.{{ $i }}.content" rows="2" placeholder="Jawaban..."
                                  class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                    </div>
                @endforeach

                @if (count($faqs) < 3)
                    <button wire:click="addFaqRow" type="button" class="text-sm text-teal-600 font-medium">+ Tambah pertanyaan lain</button>
                @endif
            </div>
        @endif

        {{-- STEP 4: Hubungkan WhatsApp --}}
        @if ($step === 4)
            <h2 class="text-lg font-semibold mb-1">Hubungkan WhatsApp</h2>
            <p class="text-sm text-slate-500 mb-5">Scan QR dengan nomor yang mau dipakai untuk layanan pelanggan. Bisa dilakukan nanti juga dari Dashboard.</p>

            @if ($waErrorMessage)
                <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3.5 py-3 text-left">
                    <p class="font-medium mb-0.5">Gagal terhubung ke service WhatsApp</p>
                    <p class="text-red-600">{{ $waErrorMessage }}</p>
                </div>
            @endif

            <div wire:poll.3s="refreshWhatsappStatus" class="text-center py-4">
                @if (! $waSession || $waSession->status === 'disconnected')
                    <button wire:click="connectWhatsapp" wire:loading.attr="disabled" wire:target="connectWhatsapp"
                            class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2 rounded-lg inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="connectWhatsapp">Tampilkan QR</span>
                        <span wire:loading wire:target="connectWhatsapp" class="flex items-center gap-2">
                            <span class="btn-spinner"></span> Menyiapkan...
                        </span>
                    </button>
                @elseif ($waSession->status === 'qr_pending')
                    @if ($waSession->qr_code)
                        <img src="data:image/png;base64,{{ $waSession->qr_code }}" class="mx-auto w-52 h-52 border border-slate-200 rounded-lg">
                    @else
                        <p class="text-xs text-slate-400">Menunggu QR dari server...</p>
                        <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto">Kalau lebih dari 15 detik, cek apakah Node service WhatsApp sudah dijalankan.</p>
                    @endif
                    <p class="text-xs text-slate-400 mt-3">Buka WhatsApp di HP → Perangkat Tertaut → Scan kode ini.</p>
                @elseif ($waSession->status === 'connected')
                    <div class="text-teal-600 text-3xl mb-2">✓</div>
                    <p class="text-sm font-medium">Terhubung dengan {{ $waSession->phone_number }}</p>
                @endif
            </div>
        @endif

        {{-- Navigasi --}}
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
            <button wire:click="prevStep" type="button" wire:loading.attr="disabled" wire:target="prevStep,nextStep,finish"
                    class="text-sm text-slate-500 disabled:opacity-50 {{ $step === 1 ? 'invisible' : '' }}">
                ← Kembali
            </button>

            <div class="flex items-center gap-3">
                @if ($step === 4)
                    <button wire:click="skipWhatsapp" type="button" wire:loading.attr="disabled" wire:target="skipWhatsapp,finish"
                            class="text-sm text-slate-500 disabled:opacity-50">Lewati untuk sekarang</button>
                    <button wire:click="finish" wire:loading.attr="disabled" wire:target="finish,skipWhatsapp"
                            class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white text-sm font-medium px-5 py-2 rounded-lg inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="finish,skipWhatsapp">Selesai & Masuk ke Dashboard</span>
                        <span wire:loading wire:target="finish,skipWhatsapp" class="flex items-center gap-2">
                            <span class="btn-spinner"></span> Menyiapkan dashboard...
                        </span>
                    </button>
                @else
                    <button wire:click="nextStep" wire:loading.attr="disabled" wire:target="nextStep"
                            class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white text-sm font-medium px-5 py-2 rounded-lg inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="nextStep">Lanjut →</span>
                        <span wire:loading wire:target="nextStep" class="flex items-center gap-2">
                            <span class="btn-spinner"></span> Menyimpan...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
