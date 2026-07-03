<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-5 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Agen Tim (CS)</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola hak akses masuk karyawan Anda. Paket aktif saat ini:
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-700 uppercase">{{ Auth::user()->tenant->plan }}</span>
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button wire:click="openModal"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-semibold rounded-lg text-white bg-teal-600 hover:bg-teal-700 transition">
                + Tambah Agen Baru
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-sm font-medium text-green-800">
            {{ session('success') }}</div>
    @elseif (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-sm font-medium text-red-800">
            {{ session('error') }}</div>
    @endif

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase">Nama Lengkap</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase">Alamat Email</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase">Terdaftar Pada</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr class="bg-slate-50/50 font-medium">
                    <td class="px-6 py-4 text-gray-900">{{ Auth::user()->name }} (Anda)</td>
                    <td class="px-6 py-4 text-gray-500">{{ Auth::user()->email }}</td>
                    <td class="px-6 py-4"><span
                            class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded text-xs font-bold uppercase">Owner</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">-</td>
                    <td class="px-6 py-4 text-right text-gray-400 font-normal italic text-xs">Pemilik Utama</td>
                </tr>

                @forelse($agents as $agent)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $agent->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $agent->email }}</td>
                        <td class="px-6 py-4"><span
                                class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-bold uppercase">Agent
                                / CS</span></td>
                        <td class="px-6 py-4 text-gray-500">{{ $agent->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <button
                                onclick="confirm('Yakin ingin mencabut akses login agen ini?') || event.stopImmediatePropagation()"
                                wire:click="deleteAgent({{ $agent->id }})"
                                class="text-red-600 hover:text-red-900 font-semibold">
                                Cabut Akses
                            </button>
                        </td>
                    </tr>
                @empty
                    @if (Auth::user()->tenant->plan === 'starter' || Auth::user()->tenant->plan === 'trial')
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-amber-600 font-medium bg-amber-50/30">
                                💡 Paket **Starter** Anda terkunci untuk 1 pengguna utama (Owner). Upgrade ke paket
                                **Pro** untuk menambah sub-akun karyawan CS.
                            </td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-0" role="dialog"
            aria-modal="true" aria-labelledby="modal-title">
            <div class="fixed inset-0 bg-slate-900/50 transition-opacity duration-300"
                wire:click="$set('isModalOpen', false)"></div>

            <div
                class="inline-block bg-white rounded-xl text-left overflow-hidden shadow-2xl transform-gpu transition-all sm:my-8 sm:max-w-md sm:w-full border border-slate-100 relative z-10 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Daftarkan Akun Agen Baru</h3>

                <form wire:submit.prevent="storeAgent" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nama Lengkap Karyawan</label>
                        <input type="text" wire:model="name"
                            class="mt-1.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg shadow-none"
                            placeholder="Contoh: Ahmad Fauzi" autocomplete="off">
                        @error('name')
                            <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Email Login Agen</label>
                        <input type="email" wire:model="email"
                            class="mt-1.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg shadow-none"
                            placeholder="ahmad@company.com" autocomplete="off">
                        @error('email')
                            <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Password Login Karyawan</label>
                        <input type="password" wire:model="password"
                            class="mt-1.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded-lg shadow-none"
                            placeholder="Minimal 8 karakter rahasia">
                        @error('password')
                            <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-6 flex flex-row-reverse gap-2 border-t pt-4 border-gray-100">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg px-4 py-2 bg-teal-600 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            <span wire:loading.remove wire:target="storeAgent">Simpan Pengaturan</span>
                            <span wire:loading wire:target="storeAgent" class="flex items-center gap-2">
                                <span class="btn-spinner"></span> Menyimpan...
                            </span>
                        </button>
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
