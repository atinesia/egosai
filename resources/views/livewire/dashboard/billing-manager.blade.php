<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.10s>
    <div class="border-b border-gray-200 pb-5 mb-8">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Langganan & Billing</h1>
        <p class="mt-1 text-sm text-gray-500">
            Status Paket Anda saat ini: <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 uppercase">{{ Auth::user()->tenant->plan }}</span>
            @if (Auth::user()->tenant->expired_at)
                · Berlaku hingga <span
                    class="font-medium text-gray-700">{{ \Carbon\Carbon::parse(Auth::user()->tenant->expired_at)->format('d M Y') }}</span>
            @endif
        </p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-sm font-medium text-green-800">
            {{ session('success') }}</div>
    @elseif (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-sm font-medium text-red-800">
            {{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        @foreach ($plans as $key => $plan)
            <div
                class="bg-white rounded-xl border {{ Auth::user()->tenant->plan === $key ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-gray-200' }} shadow-sm p-6 flex flex-col justify-between relative overflow-hidden">
                @if (Auth::user()->tenant->plan === $key)
                    <div
                        class="absolute top-0 right-0 bg-teal-600 text-white text-[10px] uppercase font-bold tracking-wider px-3 py-1 rounded-bl-lg">
                        Aktif</div>
                @endif
                <div>
                    <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">{{ $plan['name'] }}</h3>
                    <div class="mt-4 flex items-baseline text-gray-900">
                        <span class="text-3xl font-extrabold tracking-tight">Rp
                            {{ number_format($plan['price'], 0, ',', '.') }}</span>
                        <span class="ml-1 text-sm font-semibold text-gray-500">/bulan</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <span class="text-teal-600">✓</span> Maksimal <strong>{{ $plan['whatsapp_limit'] }} Nomor
                                WhatsApp</strong>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-teal-600">✓</span> Full AI Auto Reply & Knowledge Base
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-teal-600">✓</span> Real-time Live Chat Inbox
                        </li>
                    </ul>
                </div>
                <div class="mt-8">
                    <button wire:click="selectPlan('{{ $key }}')"
                        class="w-full py-2 px-4 text-center text-sm font-semibold rounded-lg text-white bg-teal-600 hover:bg-teal-700 transition">
                        {{ Auth::user()->tenant->plan === $key ? 'Perpanjang Paket' : 'Pilih Paket ' . $plan['name'] }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900">Riwayat Transaksi Tagihan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">No. Invoice
                        </th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Paket</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $order->merchant_ref }}</td>
                            <td class="px-6 py-4 uppercase font-semibold text-xs text-gray-600">{{ $order->plan }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-500">{{ $order->payment_method }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">Rp
                                {{ number_format($order->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'unpaid' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $order->status === 'expired' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $order->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($order->status === 'unpaid' && $order->checkout_url)
                                    <a href="{{ $order->checkout_url }}" target="_blank"
                                        class="text-teal-600 hover:text-teal-900 font-semibold">Bayar Sekarang →</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada riwayat transaksi
                                pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isCheckoutModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-0" role="dialog"
            aria-modal="true" aria-labelledby="modal-title">
            <div class="fixed inset-0 bg-slate-900/50 transition-opacity duration-300"
                wire:click="$set('isCheckoutModalOpen', false)"></div>

            <div
                class="inline-block bg-white rounded-xl text-left overflow-hidden shadow-2xl transform-gpu transition-all sm:my-8 sm:max-w-md sm:w-full border border-slate-100 relative z-10 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2" id="modal-title">Metode Pembayaran Tripay</h3>
                <p class="text-xs text-gray-500 mb-5">Anda akan melakukan checkout untuk paket <strong
                        class="uppercase text-teal-600">{{ $selectedPlan }}</strong> senilai <strong>Rp
                        {{ number_format($plans[$selectedPlan]['price'], 0, ',', '.') }}</strong>.</p>

                <div class="space-y-4 max-h-60 overflow-y-auto pr-1">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Channel Pembayaran</label>
                        <select wire:model="selectedMethod"
                            class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm shadow-none">
                            <option value="">-- Pilih Metode --</option>
                            @foreach ($paymentChannels as $channel)
                                <option value="{{ $channel['code'] }}">{{ $channel['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex flex-row-reverse gap-2 border-t pt-4 border-gray-100">
                    <button type="button" wire:click="processCheckout"
                        class="w-full inline-flex justify-center rounded-lg px-4 py-2 bg-teal-600 text-sm font-semibold text-white hover:bg-teal-700 transition">
                        Lanjut ke Pembayaran
                    </button>
                    <button type="button" wire:click="$set('isCheckoutModalOpen', false)"
                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
