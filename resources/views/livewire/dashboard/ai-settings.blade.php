<div class="p-6 max-w-2xl">
    <h1 class="text-lg font-semibold mb-4">Pengaturan AI</h1>

    @if (session('saved'))
        <div class="mb-4 text-sm text-teal-700 bg-teal-50 border border-teal-200 rounded-lg px-3 py-2">{{ session('saved') }}</div>
    @endif

    <form wire:submit="save" class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="is_ai_globally_active">
            Aktifkan AI auto-reply untuk seluruh percakapan masuk
        </label>

        {{-- <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Model Groq</label>
            <select wire:model="model" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="llama-3.3-70b-versatile">llama-3.3-70b-versatile (seimbang)</option>
                <option value="llama-3.1-8b-instant">llama-3.1-8b-instant (cepat & murah)</option>
                <option value="mixtral-8x7b-32768">mixtral-8x7b-32768</option>
            </select>
        </div> --}}

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Prompt Kepribadian AI</label>
            <textarea wire:model="system_prompt" rows="5" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kreativitas (temperature: {{ $temperature }})</label>
            <input type="range" min="0" max="1" step="0.1" wire:model="temperature" class="w-full">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Pesan Fallback (jika AI gagal/tidak tahu)</label>
            <input type="text" wire:model="fallback_message" class="w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                class="bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
            <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
            <span wire:loading wire:target="save" class="flex items-center gap-2">
                <span class="btn-spinner"></span> Menyimpan...
            </span>
        </button>
    </form>
</div>
