<form wire:submit="register" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Bisnis</label>
        <input type="text" wire:model="business_name" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500" placeholder="Toko Maju Jaya">
        @error('business_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Anda</label>
        <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" wire:model="email" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
        <input type="password" wire:model="password" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
        <input type="password" wire:model="password_confirmation" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
    </div>
    <button type="submit" wire:loading.attr="disabled" wire:target="register"
            class="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg flex items-center justify-center gap-2">
        <span wire:loading.remove wire:target="register">Daftar & Buat Workspace</span>
        <span wire:loading wire:target="register" class="flex items-center gap-2">
            <span class="btn-spinner"></span> Membuat workspace...
        </span>
    </button>
    <p class="text-center text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="text-teal-600 font-medium">Masuk</a></p>
</form>
