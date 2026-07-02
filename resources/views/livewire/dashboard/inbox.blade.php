<div class="flex h-screen">

    <!-- Sidebar kiri: filter + daftar percakapan -->
    <div class="w-80 border-r border-slate-200 bg-white flex flex-col">

        <!-- Search -->
        <div class="p-3 border-b border-slate-200 space-y-2">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari kontak..."
                class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">

            <!-- Filter per nomor WhatsApp -->
            @if ($sessions->count() > 1)
                <select wire:model.live="filterSessionId"
                    class="w-full rounded-lg border-slate-300 text-slate-600 focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Semua nomor ({{ $sessions->count() }})</option>
                    @foreach ($sessions as $sess)
                        <option value="{{ $sess->id }}">
                            {{ $sess->label }}{{ $sess->phone_number ? ' · ' . $sess->phone_number : '' }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- List percakapan -->
        <div class="flex-1 overflow-y-auto">
            @forelse ($conversations as $conv)
                <button wire:click="selectConversation({{ $conv->id }})"
                    class="w-full text-left px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors
                               {{ $activeConversationId === $conv->id ? 'bg-teal-50' : '' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-sm text-slate-900 truncate">
                            {{ $conv->contact->name ?? $conv->contact->display_number }}
                        </span>
                        <div class="flex items-center gap-1 shrink-0 ml-1">
                            @if ($conv->ai_active && $conv->whatsappSession?->is_ai_active)
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-teal-100 text-teal-700">AI</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 truncate mt-0.5">
                        {{ $conv->latestMessage->content ?? '...' }}
                    </p>
                    @if ($sessions->count() > 1 && $conv->whatsappSession)
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            via {{ $conv->whatsappSession->label }}
                        </p>
                    @endif
                </button>
            @empty
                <p class="text-sm text-slate-400 text-center mt-8 px-4">
                    {{ $search ? 'Tidak ada percakapan yang cocok.' : 'Belum ada percakapan.' }}
                </p>
            @endforelse
        </div>
    </div>

    <!-- Jendela chat kanan -->
    <div class="flex-1 flex flex-col min-w-0">
        @if ($activeConversation)

            <!-- Header chat -->
            <div class="px-5 py-3.5 border-b border-slate-200 bg-white flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="font-semibold truncate">
                        {{ $activeConversation->contact->name ?? $activeConversation->contact->display_number }}
                    </h2>
                    <p class="text-xs text-slate-500 truncate">
                        {{ $activeConversation->contact->display_number }}
                        @if ($activeConversation->whatsappSession)
                            · via <span class="font-medium">{{ $activeConversation->whatsappSession->label }}</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Toggle AI per-conversation -->
                    <button wire:click="toggleAi" wire:loading.attr="disabled" wire:target="toggleAi"
                        class="text-xs px-3 py-1.5 rounded-lg border disabled:opacity-60 transition-colors
                                   {{ $activeConversation->ai_active ? 'bg-teal-600 text-white border-teal-600' : 'border-slate-300 text-slate-600' }}">
                        <span wire:loading.remove wire:target="toggleAi">
                            AI {{ $activeConversation->ai_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <span wire:loading wire:target="toggleAi" class="inline-flex items-center gap-1.5">
                            <span class="btn-spinner-dark"></span> Mengubah...
                        </span>
                    </button>

                    <button wire:click="resolveConversation" wire:loading.attr="disabled"
                        wire:target="resolveConversation"
                        class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 disabled:opacity-60">
                        <span wire:loading.remove wire:target="resolveConversation">Selesai</span>
                        <span wire:loading wire:target="resolveConversation" class="inline-flex items-center gap-1.5">
                            <span class="btn-spinner-dark"></span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Pesan -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3" wire:poll.5s>
                @foreach ($activeConversation->messages as $msg)
                    <div class="flex {{ $msg->sender_type === 'contact' ? 'justify-start' : 'justify-end' }}">
                        <div
                            class="max-w-md px-3.5 py-2.5 rounded-2xl text-sm
                            {{ $msg->sender_type === 'contact' ? 'bg-white border border-slate-200 rounded-tl-sm' : '' }}
                            {{ $msg->sender_type === 'ai' ? 'bg-teal-50 text-teal-900 border border-teal-100 rounded-tr-sm' : '' }}
                            {{ $msg->sender_type === 'agent' ? 'bg-slate-900 text-white rounded-tr-sm' : '' }}">
                            <p class="leading-relaxed">{{ $msg->content }}</p>
                            <span class="block text-[10px] opacity-50 mt-1 text-right">
                                @if ($msg->sender_type === 'ai')
                                    🤖 AI ·
                                @endif
                                @if ($msg->sender_type === 'agent')
                                    {{ $msg->user->name ?? 'Agent' }} ·
                                @endif
                                {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input balas -->
            <form wire:submit="sendReply" class="p-4 border-t border-slate-200 bg-white flex gap-2 relative"
                x-data="{
                    showDropdown: false,
                    searchQuery: '',
                    // Mengubah data PHP Collection dijadikan objek Array JavaScript lewat JSON
                    replies: {{ json_encode($quickReplies) }},
                    get filteredReplies() {
                        if (!this.searchQuery) return this.replies;
                        return this.replies.filter(r => r.shortcut.toLowerCase().includes(this.searchQuery.toLowerCase()));
                    },
                    checkInput(val) {
                        // Jika user mengetik karakter / di awal atau di posisi mana pun, picu dropdown melayang
                        if (val.startsWith('/')) {
                            this.showDropdown = true;
                            this.searchQuery = val.substring(1); // ambil text setelah garis miring untuk filter search
                        } else {
                            this.showDropdown = false;
                        }
                    },
                    selectReply(message) {
                        // Set nilai ke input model Livewire dan sembunyikan dropdown
                        @this.set('newMessage', message);
                        this.showDropdown = false;
                        // Kembalikan fokus ke kolom input chat
                        $refs.chatInput.focus();
                    }
                }">

                <div x-show="showDropdown && filteredReplies.length > 0" x-transition
                    @click.outside="showDropdown = false"
                    class="absolute bottom-20 left-4 w-80 bg-white border border-slate-200 rounded-lg shadow-xl z-50 max-h-60 overflow-y-auto">
                    <div
                        class="p-2 bg-slate-50 border-b border-slate-100 text-xxs font-semibold text-slate-400 tracking-wider uppercase">
                        Pilih Balasan Cepat (Pintasan)
                    </div>
                    <template x-for="item in filteredReplies" :key="item.id">
                        <button type="button" @click="selectReply(item.message)"
                            class="w-full text-left px-4 py-2.5 hover:bg-teal-50 border-b border-slate-50 last:border-0 transition flex flex-col">
                            <span class="text-xs font-bold text-teal-600 font-mono" x-text="'/' + item.shortcut"></span>
                            <span class="text-xs text-slate-500 truncate w-full mt-0.5" x-text="item.message"></span>
                        </button>
                    </template>
                </div>

                <input type="text" wire:model="newMessage" x-ref="chatInput" @input="checkInput($event.target.value)"
                    @keydown.escape="showDropdown = false"
                    placeholder="Tulis balasan manual... (Ketik '/' untuk menggunakan template balasan cepat)"
                    wire:loading.attr="disabled" wire:target="sendReply"
                    class="flex-1 rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500 disabled:bg-slate-50 text-sm">
                <button type="submit" wire:loading.attr="disabled" wire:target="sendReply"
                    class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed
                               text-white px-4 rounded-lg text-sm font-medium flex items-center gap-2 min-w-22 justify-center">
                    <span wire:loading.remove wire:target="sendReply">Kirim</span>
                    <span wire:loading wire:target="sendReply" class="btn-spinner"></span>
                </button>
            </form>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-slate-400 text-sm gap-2">
                <span class="text-3xl">💬</span>
                <p>Pilih percakapan untuk mulai membalas.</p>
            </div>
        @endif
    </div>
    <!-- Elemen Audio Tersembunyi (Gunakan file audio ringkas .mp3 bebas royalti pilihan Anda) -->
    <audio id="notifSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-600.wav" preload="auto"></audio>

    <script>
        document.addEventListener('livewire:init', () => {
            // Mendengarkan trigger audio dari backend Livewire
            Livewire.on('play-notification-sound', () => {
                const audio = document.getElementById('notifSound');
                if (audio) {
                    audio.currentTime = 0; // reset track ke awal
                    audio.play().catch(error => console.log(
                        "Interaksi user diperlukan untuk memutar audio:", error));
                }
            });
        });
    </script>
</div>
