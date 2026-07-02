<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AI CS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-60 bg-slate-900 text-slate-200 flex flex-col">
            <div class="px-5 py-5 flex items-center gap-2 border-b border-slate-800">
                <div class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center font-bold text-slate-900">AI
                </div>
                <span class="font-semibold truncate">{{ auth()->user()->tenant->name ?? 'Workspace' }}</span>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="{{ route('dashboard.inbox') }}"
                    class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.inbox') ? 'bg-slate-800 text-white' : '' }}">
                    <span>💬 Inbox</span>
                    {{-- Badge unread — diisi Alpine.js dari data notifikasi --}}
                    <span x-data x-show="$store.notif.unread > 0"
                        x-text="$store.notif.unread > 99 ? '99+' : $store.notif.unread"
                        class="text-[10px] bg-red-500 text-white rounded-full px-1.5 py-0.5 font-medium min-w-[18px] text-center">
                    </span>
                </a>
                <a href="{{ route('dashboard.contacts') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.contacts') ? 'bg-slate-800 text-white' : '' }}">👥
                    Kontak</a>
                <a href="{{ route('dashboard.reports') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.reports') ? 'bg-slate-800 text-white' : '' }}">📊
                    Laporan</a>
                <a href="{{ route('dashboard.knowledge-base') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.knowledge-base') ? 'bg-slate-800 text-white' : '' }}">📚
                    Knowledge Base</a>
                <a href="{{ route('dashboard.ai-settings') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.ai-settings') ? 'bg-slate-800 text-white' : '' }}">🤖
                    Pengaturan AI</a>
                <a href="{{ route('dashboard.whatsapp') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.whatsapp') ? 'bg-slate-800 text-white' : '' }}">📱
                    Nomor WhatsApp</a>
                <a href="{{ route('dashboard.quick-replies') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard.quick-replies') ? 'bg-slate-800 text-white' : '' }}">⚡
                    Balasan Cepat</a>
            </nav>

            <!-- Notif bell + avatar di footer sidebar -->
            <div class="px-3 py-4 border-t border-slate-800 space-y-1">

                <!-- Bell dropdown notifikasi -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open; if(open) $store.notif.markRead()"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-800 text-sm">
                        <span>🔔 Notifikasi</span>
                        <span x-show="$store.notif.unread > 0"
                            x-text="$store.notif.unread > 9 ? '9+' : $store.notif.unread"
                            class="text-[10px] bg-red-500 text-white rounded-full px-1.5 py-0.5 font-medium">
                        </span>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute bottom-full left-0 mb-1 w-72 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50">
                        <div class="px-4 py-2.5 border-b border-slate-100 flex justify-between items-center">
                            <p class="text-xs font-semibold text-slate-700">Pesan Masuk Terbaru</p>
                            <button @click="$store.notif.clearAll()"
                                class="text-[10px] text-slate-400 hover:text-slate-600">Hapus semua</button>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                            <template x-for="n in $store.notif.items" :key="n.id">
                                <a :href="'/dashboard/inbox?conv=' + n.conversation_id"
                                    class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors block">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm shrink-0 font-medium"
                                        x-text="n.contact_name.charAt(0).toUpperCase()"></div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-900 truncate"
                                            x-text="n.contact_name"></p>
                                        <p class="text-xs text-slate-500 truncate" x-text="n.message_preview"></p>
                                        <p class="text-[10px] text-slate-400 mt-0.5" x-text="n.session_label"></p>
                                    </div>
                                </a>
                            </template>
                            <div x-show="$store.notif.items.length === 0"
                                class="px-4 py-6 text-center text-xs text-slate-400">
                                Belum ada notifikasi.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" x-data="{ submitting: false }"
                    @submit="submitting = true">
                    @csrf
                    <button type="submit" :disabled="submitting"
                        class="w-full text-left text-sm px-3 py-2 rounded-lg hover:bg-slate-800 disabled:opacity-60 flex items-center gap-2">
                        <span x-show="!submitting">⎋ Keluar</span>
                        <span x-show="submitting" class="flex items-center gap-2"><span class="btn-spinner"></span>
                            Keluar...</span>
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
    @stack('scripts')

    {{-- Data user untuk JS --}}
    <script>
        window.__TENANT_ID__ = {{ auth()->user()->tenant_id ?? 'null' }};
        window.__USER_ID__ = {{ auth()->id() ?? 'null' }};
    </script>
    @viteReactRefresh
</body>

</html>
