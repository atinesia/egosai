<form wire:submit="login" class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" wire:model="email" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
        <input type="password" wire:model="password" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500">
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" wire:model="remember"> Ingat saya
    </label>
    <button type="submit" wire:loading.attr="disabled" wire:target="login"
            class="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-70 disabled:cursor-not-allowed text-white font-medium py-2.5 rounded-lg flex items-center justify-center gap-2">
        <span wire:loading.remove wire:target="login">Masuk</span>
        <span wire:loading wire:target="login" class="flex items-center gap-2">
            <span class="btn-spinner"></span> Memeriksa...
        </span>
    </button>
    <p class="text-center text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="text-teal-600 font-medium">Daftar</a></p>
</form>
