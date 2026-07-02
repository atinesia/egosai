<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-5 mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Quick Reply</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola template balasan cepat untuk mempermudah CS membalas pesan
                manual.</p>
        </div>
        <div>
            <button wire:click="create()"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition">
                + Tambah Balasan Cepat
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                    Pintasan (Shortcut)</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/4">
                                    Isi Pesan Template</th>
                                <th scope="col" class="relative px-6 py-3 w-1/4">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quickReplies as $reply)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-md text-sm font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            /{{ $reply->shortcut }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-lg truncate"
                                            title="{{ $reply->message }}">
                                            {{ $reply->message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <button wire:click="edit({{ $reply->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold focus:outline-none">Edit</button>
                                        <button wire:click="delete({{ $reply->id }})"
                                            onclick="confirm('Apakah Anda yakin ingin menghapus balasan cepat ini?') || event.stopImmediatePropagation()"
                                            class="text-red-600 hover:text-red-900 font-semibold focus:outline-none">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-sm text-gray-500">
                                        Belum ada data template balasan cepat. Klik tombol di kanan atas untuk membuat
                                        baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-slate-900/50  transition-opacity duration-300 ease-out" wire:click="closeModal()">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform-gpu transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 z-10">
                    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 mb-5" id="modal-title">
                                {{ $isEditMode ? 'Edit Balasan Cepat' : 'Tambah Balasan Cepat Baru' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="shortcut" class="block text-sm font-semibold text-slate-700">Pintasan
                                        (Shortcut)</label>
                                    <div class="mt-1.5 relative rounded-md shadow-sm">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-400 sm:text-sm font-mono font-bold">/</span>
                                        </div>
                                        <input type="text" wire:model="shortcut" id="shortcut"
                                            class="focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full pl-7 pr-12 sm:text-sm border-slate-300 rounded-lg shadow-none"
                                            placeholder="contoh: ongkir" autocomplete="off">
                                    </div>
                                    @error('shortcut')
                                        <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-semibold text-slate-700">Isi Teks
                                        Balasan</label>
                                    <div class="mt-1.5">
                                        <textarea id="message" wire:model="message" rows="5"
                                            class="shadow-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-slate-300 rounded-lg text-slate-800"
                                            placeholder="Tuliskan isi pesan template yang panjang di sini..."></textarea>
                                    </div>
                                    @error('message')
                                        <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-slate-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-2 border-t border-slate-100 mt-4">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-sm font-semibold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-2 sm:w-auto transition-colors">
                                Simpan Data
                            </button>
                            <button type="button" wire:click="closeModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-200 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:w-auto transition-colors">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
