<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AI CS') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-60 bg-slate-900 text-slate-200 flex flex-col">
            <div class="px-5 py-5 flex items-center gap-2 border-b border-slate-800">
                <div class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center font-bold text-slate-900">AI</div>
                <span class="font-semibold">{{ auth()->user()->tenant->name ?? 'Workspace' }}</span>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="{{ route('dashboard.inbox') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.inbox') ? 'bg-slate-800 text-white' : '' }}">💬 Inbox</a>
                <a href="{{ route('dashboard.contacts') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.contacts') ? 'bg-slate-800 text-white' : '' }}">👥 Kontak</a>
                <a href="{{ route('dashboard.knowledge-base') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.knowledge-base') ? 'bg-slate-800 text-white' : '' }}">📚 Knowledge Base</a>
                <a href="{{ route('dashboard.ai-settings') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.ai-settings') ? 'bg-slate-800 text-white' : '' }}">🤖 Pengaturan AI</a>
                <a href="{{ route('dashboard.whatsapp') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.whatsapp') ? 'bg-slate-800 text-white' : '' }}">📱 Nomor WhatsApp</a>
            </nav>
            <div class="px-3 py-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    <button type="submit" :disabled="submitting" class="w-full text-left text-sm px-3 py-2 rounded-lg hover:bg-slate-800 disabled:opacity-60 flex items-center gap-2">
                        <span x-show="!submitting">⎋ Keluar</span>
                        <span x-show="submitting" class="flex items-center gap-2"><span class="btn-spinner"></span> Keluar...</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
