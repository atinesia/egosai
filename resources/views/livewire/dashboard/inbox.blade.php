<div class="flex h-screen" x-data>
    <!-- Daftar percakapan -->
    <div class="w-80 border-r border-slate-200 bg-white flex flex-col">
        <div class="p-4 border-b border-slate-200">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari kontak..."
                   class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
        </div>
        <div class="flex-1 overflow-y-auto" wire:poll.5s>
            @forelse ($conversations as $conv)
                <button wire:click="selectConversation({{ $conv->id }})"
                        class="w-full text-left px-4 py-3 border-b border-slate-100 hover:bg-slate-50 {{ $activeConversationId === $conv->id ? 'bg-teal-50' : '' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-sm text-slate-900">{{ $conv->contact->name ?? $conv->contact->wa_number }}</span>
                        @if($conv->ai_active)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-teal-100 text-teal-700">AI</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ $conv->latestMessage->content ?? '...' }}</p>
                </button>
            @empty
                <p class="text-sm text-slate-400 text-center mt-8">Belum ada percakapan.</p>
            @endforelse
        </div>
    </div>

    <!-- Jendela chat -->
    <div class="flex-1 flex flex-col">
        @if ($activeConversation)
            <div class="p-4 border-b border-slate-200 bg-white flex items-center justify-between">
                <div>
                    <h2 class="font-semibold">{{ $activeConversation->contact->name ?? $activeConversation->contact->wa_number }}</h2>
                    <p class="text-xs text-slate-500">{{ $activeConversation->contact->wa_number }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="toggleAi" wire:loading.attr="disabled" wire:target="toggleAi"
                            class="text-xs px-3 py-1.5 rounded-lg border disabled:opacity-60 disabled:cursor-not-allowed transition-colors {{ $activeConversation->ai_active ? 'bg-teal-600 text-white border-teal-600' : 'border-slate-300 text-slate-600' }}">
                        <span wire:loading.remove wire:target="toggleAi">AI {{ $activeConversation->ai_active ? 'Aktif' : 'Nonaktif' }}</span>
                        <span wire:loading wire:target="toggleAi" class="inline-flex items-center gap-1.5">
                            <span class="btn-spinner-dark"></span> Mengubah...
                        </span>
                    </button>
                    <button wire:click="resolveConversation" wire:loading.attr="disabled" wire:target="resolveConversation"
                            class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="resolveConversation">Tandai Selesai</span>
                        <span wire:loading wire:target="resolveConversation" class="inline-flex items-center gap-1.5">
                            <span class="btn-spinner-dark"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3" wire:poll.5s>
                @foreach ($activeConversation->messages as $msg)
                    <div class="flex {{ $msg->sender_type === 'contact' ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-md px-3 py-2 rounded-xl text-sm
                            {{ $msg->sender_type === 'contact' ? 'bg-white border border-slate-200' : '' }}
                            {{ $msg->sender_type === 'ai' ? 'bg-teal-100 text-teal-900' : '' }}
                            {{ $msg->sender_type === 'agent' ? 'bg-slate-900 text-white' : '' }}">
                            <p>{{ $msg->content }}</p>
                            <span class="block text-[10px] opacity-60 mt-1">
                                {{ $msg->sender_type === 'ai' ? 'AI' : ($msg->sender_type === 'agent' ? ($msg->user->name ?? 'Agent') : '') }}
                                · {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <form wire:submit="sendReply" class="p-4 border-t border-slate-200 bg-white flex gap-2">
                <input type="text" wire:model="newMessage" placeholder="Tulis balasan manual..." wire:loading.attr="disabled" wire:target="sendReply"
                       class="flex-1 rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500 disabled:bg-slate-50">
                <button type="submit" wire:loading.attr="disabled" wire:target="sendReply"
                        class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white px-4 rounded-lg text-sm font-medium flex items-center gap-2 min-w-[88px] justify-center">
                    <span wire:loading.remove wire:target="sendReply">Kirim</span>
                    <span wire:loading wire:target="sendReply" class="btn-spinner"></span>
                </button>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-slate-400 text-sm">
                Pilih percakapan di sebelah kiri untuk mulai membalas.
            </div>
        @endif
    </div>
</div>
