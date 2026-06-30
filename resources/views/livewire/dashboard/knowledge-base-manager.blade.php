<div class="p-6 grid grid-cols-3 gap-6">
    <div class="col-span-1 bg-white rounded-xl border border-slate-200 p-4">
        <h2 class="font-semibold mb-3">{{ $editingId ? 'Edit Materi' : 'Tambah Materi Baru' }}</h2>
        <form wire:submit="save" class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Judul</label>
                <input type="text" wire:model="title" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Contoh: Kebijakan Pengembalian">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Isi / Jawaban</label>
                <textarea wire:model="content" rows="8" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Tulis informasi yang akan dipakai AI untuk menjawab pelanggan..."></textarea>
                @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white text-sm font-medium py-2 rounded-lg flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Materi' : 'Simpan Materi' }}</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <span class="btn-spinner"></span> Menyimpan...
                </span>
            </button>
        </form>
    </div>

    <div class="col-span-2 bg-white rounded-xl border border-slate-200">
        <div class="p-4 border-b border-slate-200">
            <h2 class="font-semibold">Knowledge Base ({{ $items->count() }})</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach ($items as $kb)
                <div class="p-4 flex justify-between items-start gap-4">
                    <div>
                        <p class="font-medium text-sm">{{ $kb->title }}</p>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $kb->content }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="toggleActive({{ $kb->id }})" wire:loading.attr="disabled" wire:target="toggleActive({{ $kb->id }})"
                                class="text-[10px] px-2 py-1 rounded disabled:opacity-60 transition-colors {{ $kb->is_active ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-500' }}">
                            <span wire:loading.remove wire:target="toggleActive({{ $kb->id }})">{{ $kb->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            <span wire:loading wire:target="toggleActive({{ $kb->id }})">...</span>
                        </button>
                        <button wire:click="edit({{ $kb->id }})" class="text-xs text-slate-500 hover:text-slate-900">Edit</button>
                        <button wire:click="delete({{ $kb->id }})" wire:confirm="Hapus materi ini?" wire:loading.attr="disabled" wire:target="delete({{ $kb->id }})"
                                class="text-xs text-red-500 hover:text-red-700 disabled:opacity-60">
                            <span wire:loading.remove wire:target="delete({{ $kb->id }})">Hapus</span>
                            <span wire:loading wire:target="delete({{ $kb->id }})">Menghapus...</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
