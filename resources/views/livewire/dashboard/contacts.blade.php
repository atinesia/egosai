<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold">Kontak</h1>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari kontak..."
               class="rounded-lg border-slate-300 text-sm w-64 focus:border-teal-500 focus:ring-teal-500">
    </div>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Nomor WhatsApp</th>
                    <th class="px-4 py-2">Bergabung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($contacts as $c)
                    <tr>
                        <td class="px-4 py-2.5">{{ $c->name ?? '-' }}</td>
                        <td class="px-4 py-2.5">{{ $c->wa_number }}</td>
                        <td class="px-4 py-2.5 text-slate-500">{{ $c->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $contacts->links() }}</div>
</div>
