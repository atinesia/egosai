<div class="p-6 space-y-6 max-w-6xl">

    {{-- Header + filter periode --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold">Laporan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Performa CS & AI dalam periode yang dipilih</p>
        </div>
        <div class="flex gap-1 bg-slate-100 rounded-lg p-1">
            @foreach (['7' => '7 Hari', '30' => '30 Hari', '90' => '90 Hari'] as $val => $label)
                <button wire:click="$set('period', '{{ $val }}')"
                        class="text-sm px-3 py-1.5 rounded-md font-medium transition-colors
                               {{ $period === $val ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Pesan Masuk',     'value' => number_format($incomingMessages),   'sub' => 'dari pelanggan',          'icon' => '📨', 'color' => 'teal'],
                ['label' => 'Dibalas AI',       'value' => number_format($aiReplies),          'sub' => ($incomingMessages > 0 ? round($aiReplies / $incomingMessages * 100) : 0) . '% dari pesan masuk', 'icon' => '🤖', 'color' => 'teal'],
                ['label' => 'Dibalas Agent',    'value' => number_format($agentReplies),       'sub' => ($incomingMessages > 0 ? round($agentReplies / $incomingMessages * 100) : 0) . '% dari pesan masuk', 'icon' => '👤', 'color' => 'slate'],
                ['label' => 'Kontak Baru',      'value' => number_format($newContacts),        'sub' => $days . ' hari terakhir',  'icon' => '👥', 'color' => 'slate'],
                ['label' => 'Selesai',          'value' => number_format($resolvedConversations), 'sub' => 'percakapan diselesaikan','icon' => '✅', 'color' => 'teal'],
                ['label' => 'Masih Terbuka',    'value' => number_format($openConversations),  'sub' => 'percakapan aktif',        'icon' => '💬', 'color' => 'amber'],
                ['label' => 'Total Pesan',      'value' => number_format($totalMessages),      'sub' => 'semua pengirim',          'icon' => '📊', 'color' => 'slate'],
                ['label' => 'Rata-rata Respons',
                 'value' => $avgResponseSeconds !== null
                    ? ($avgResponseSeconds < 60
                        ? round($avgResponseSeconds) . ' dtk'
                        : round($avgResponseSeconds / 60, 1) . ' mnt')
                    : '—',
                 'sub' => 'waktu respons pertama', 'icon' => '⚡', 'color' => 'teal'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold mt-0.5 tracking-tight">{{ $card['value'] }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $card['sub'] }}</p>
                    </div>
                    <span class="text-xl">{{ $card['icon'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Chart pesan per hari ── --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mt-4">
        <h2 class="text-sm font-semibold text-slate-700 mb-4">Tren Pesan ({{ $days }} Hari Terakhir)</h2>
        <div class="relative" style="height: 220px">
            <canvas id="daily-chart"></canvas>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4 mt-4">

        {{-- ── Tabel top kontak ── --}}
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Top 5 Kontak Paling Aktif</h2>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse ($topContacts as $i => $c)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <span class="text-xs font-bold text-slate-300 w-4">{{ $i + 1 }}</span>
                        <div class="w-7 h-7 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-semibold shrink-0">
                            {{ strtoupper(substr($c->name ?? $c->wa_number, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $c->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ \App\Models\Contact::make(['wa_number' => $c->wa_number])->display_number }}</p>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">{{ number_format($c->total) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- ── Distribusi AI vs Agent (donut) ── --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Distribusi Balasan</h2>
            <div class="flex items-center gap-6">
                <div class="relative" style="width:140px;height:140px;flex-shrink:0">
                    <canvas id="donut-chart"></canvas>
                </div>
                <div class="space-y-2.5">
                    @php $total = array_sum(array_values($senderDist)); @endphp
                    @foreach ($senderDist as $label => $count)
                        @php $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0
                                {{ $label === 'Pelanggan' ? 'bg-slate-300' : ($label === 'AI' ? 'bg-teal-500' : 'bg-slate-600') }}">
                            </span>
                            <span class="text-sm text-slate-600">{{ $label }}</span>
                            <span class="ml-auto text-sm font-semibold text-slate-800">{{ $pct }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Performa per nomor WhatsApp ── --}}
    @if ($sessionStats->count() > 1)
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Performa per Nomor WhatsApp</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-500 bg-slate-50">
                            <th class="px-5 py-2.5 font-medium">Nomor</th>
                            <th class="px-4 py-2.5 font-medium">Status</th>
                            <th class="px-4 py-2.5 font-medium text-right">Masuk</th>
                            <th class="px-4 py-2.5 font-medium text-right">AI</th>
                            <th class="px-4 py-2.5 font-medium text-right">Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sessionStats as $s)
                            <tr>
                                <td class="px-5 py-3">
                                    <p class="font-medium">{{ $s['label'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $s['phone'] }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-[11px] px-2 py-0.5 rounded-full
                                        {{ $s['status'] === 'Terhubung' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $s['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($s['incoming']) }}</td>
                                <td class="px-4 py-3 text-right text-teal-700 font-medium">{{ number_format($s['ai']) }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($s['agent']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('livewire:navigated', initCharts);
document.addEventListener('DOMContentLoaded', initCharts);

function initCharts() {
    // Daily line chart
    const dailyEl = document.getElementById('daily-chart');
    if (dailyEl) {
        if (dailyEl._chartInstance) dailyEl._chartInstance.destroy();
        dailyEl._chartInstance = new Chart(dailyEl, {
            type: 'line',
            data: {
                labels: @json($dailyData['labels']),
                datasets: [
                    {
                        label: 'Pesan Masuk',
                        data: @json($dailyData['incoming']),
                        borderColor: '#0D9488',
                        backgroundColor: 'rgba(13,148,136,0.08)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                    },
                    {
                        label: 'Balasan (AI + Agent)',
                        data: @json($dailyData['replies']),
                        borderColor: '#94A3B8',
                        backgroundColor: 'rgba(148,163,184,0.06)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        borderDash: [4, 2],
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } },
                },
            },
        });
    }

    // Donut chart
    const donutEl = document.getElementById('donut-chart');
    if (donutEl) {
        const dist = @json($senderDist);
        if (donutEl._chartInstance) donutEl._chartInstance.destroy();
        donutEl._chartInstance = new Chart(donutEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(dist),
                datasets: [{
                    data: Object.values(dist),
                    backgroundColor: ['#CBD5E1', '#0D9488', '#475569'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } },
            },
        });
    }
}

// Re-init chart saat Livewire update periode (wire:click)
document.addEventListener('livewire:update', () => setTimeout(initCharts, 50));
</script>
@endpush
