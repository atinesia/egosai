<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Balas') }} — CS &amp; Sales AI untuk WhatsApp Bisnis Kamu</title>
    <meta name="description" content="Hubungkan WhatsApp bisnismu, AI yang dilatih dari info produkmu sendiri akan membalas pelanggan dalam hitungan detik — siang malam, tanpa libur.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        :root {
            --paper: #FAF7F1;
            --ink: #0B1C2C;
            --ink-soft: #41546A;
            --teal: #0D9488;
            --teal-deep: #0A6F66;
            --mint: #E7FAF6;
            --rust: #C2542C;
        }
        body { background: var(--paper); color: var(--ink); font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ---- Signature element: animated WhatsApp preview ---- */
        .phone {
            width: 300px;
            border-radius: 36px;
            background: var(--ink);
            padding: 12px;
            box-shadow: 0 30px 60px -20px rgba(11,28,44,0.35);
        }
        .phone-screen {
            background: #ECEAE3;
            border-radius: 26px;
            overflow: hidden;
            height: 430px;
            display: flex;
            flex-direction: column;
        }
        .chat-bubble {
            opacity: 0;
            transform: translateY(6px);
            max-width: 82%;
        }
        .typing-dots span {
            animation: blink 1.2s infinite;
            display: inline-block;
        }
        .typing-dots span:nth-child(2) { animation-delay: .2s; }
        .typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes blink { 0%, 80%, 100% { opacity: .2; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-2px); } }

        @keyframes appear {
            0%   { opacity: 0; transform: translateY(6px); }
            8%   { opacity: 1; transform: translateY(0); }
            92%  { opacity: 1; transform: translateY(0); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .seq-1 { animation: appear 9s ease-out infinite; animation-delay: 0.3s; }
        .seq-2 { animation: appear 9s ease-out infinite; animation-delay: 1.4s; }
        .seq-3 { animation: appear 9s ease-out infinite; animation-delay: 4.6s; }
        .seq-4 { animation: appear 9s ease-out infinite; animation-delay: 5.7s; }

        .typing-seq { opacity: 0; animation: typingShow 9s ease-out infinite; }
        .typing-seq-1 { animation-delay: 0.8s; }
        .typing-seq-2 { animation-delay: 5.1s; }
        @keyframes typingShow {
            0%   { opacity: 0; }
            2%   { opacity: 1; }
            14%  { opacity: 1; }
            16%  { opacity: 0; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body class="antialiased">

    {{-- ===== NAV ===== --}}
    <header class="sticky top-0 z-40 backdrop-blur bg-[var(--paper)]/85 border-b border-black/5">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-[var(--teal)] flex items-center justify-center text-white font-display font-bold text-sm">B</span>
                <span class="font-display font-bold text-[15px]">{{ config('app.name', 'Balas') }}</span>
            </a>
            <nav class="hidden md:flex items-center gap-8 text-[14px] font-medium text-[var(--ink-soft)]">
                <a href="#cara-kerja" class="hover:text-[var(--ink)] transition-colors">Cara kerja</a>
                <a href="#fitur" class="hover:text-[var(--ink)] transition-colors">Fitur</a>
                <a href="#harga" class="hover:text-[var(--ink)] transition-colors">Harga</a>
                <a href="#faq" class="hover:text-[var(--ink)] transition-colors">FAQ</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline text-[14px] font-medium text-[var(--ink-soft)] hover:text-[var(--ink)] transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="bg-[var(--ink)] hover:bg-[var(--teal-deep)] transition-colors text-white text-[14px] font-medium px-4 py-2 rounded-lg">
                    Coba Gratis 14 Hari
                </a>
            </div>
        </div>
    </header>

    {{-- ===== HERO ===== --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 pt-14 pb-20 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="font-mono text-[11px] tracking-wider uppercase text-[var(--teal-deep)] bg-[var(--mint)] px-2.5 py-1 rounded-full">
                Balasan pertama rata-rata 4 detik
            </span>
            <h1 class="font-display font-extrabold text-[40px] sm:text-[52px] leading-[1.08] tracking-tight mt-5">
                Pelanggan chat jam berapa pun, <span class="text-[var(--teal)]">tetap dibalas</span> saat itu juga.
            </h1>
            <p class="text-[var(--ink-soft)] text-[17px] leading-relaxed mt-5 max-w-md">
                Hubungkan nomor WhatsApp bisnismu. AI yang sudah kamu latih dengan info produk dan SOP-mu sendiri
                akan menjawab pertanyaan pelanggan — kamu tinggal awasi dari satu dashboard, dan ambil alih kapan pun perlu.
            </p>
            <div class="flex flex-wrap items-center gap-3 mt-7">
                <a href="{{ route('register') }}" class="bg-[var(--teal)] hover:bg-[var(--teal-deep)] transition-colors text-white font-medium text-[15px] px-6 py-3 rounded-xl">
                    Mulai Gratis, Tanpa Kartu Kredit
                </a>
                <a href="#cara-kerja" class="text-[var(--ink)] font-medium text-[15px] px-5 py-3 rounded-xl border border-black/10 hover:bg-black/[0.03] transition-colors">
                    Lihat cara kerjanya
                </a>
            </div>
            <p class="text-xs text-[var(--ink-soft)] mt-4">14 hari trial penuh · Tanpa kartu kredit · Bisa di-cancel kapan saja</p>
        </div>

        {{-- Signature: animasi preview chat WhatsApp --}}
        <div class="flex justify-center lg:justify-end">
            <div class="phone">
                <div class="phone-screen">
                    <div class="bg-[var(--teal-deep)] text-white px-4 py-3 flex items-center gap-2.5 shrink-0">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm">☕</div>
                        <div>
                            <p class="text-[13px] font-semibold leading-tight">Kedai Kopi Senja</p>
                            <p class="text-[10px] text-white/70 leading-tight">online</p>
                        </div>
                    </div>
                    <div class="flex-1 px-3 py-4 space-y-2.5 overflow-hidden">
                        <div class="flex justify-end seq-1 chat-bubble ml-auto">
                            <div class="bg-white rounded-2xl rounded-tr-sm px-3.5 py-2 text-[13px] shadow-sm">
                                Halo kak, masih buka hari ini?
                            </div>
                        </div>

                        <div class="flex typing-seq typing-seq-1">
                            <div class="bg-white rounded-2xl rounded-tl-sm px-3.5 py-2.5 shadow-sm typing-dots">
                                <span class="w-1.5 h-1.5 bg-[var(--ink-soft)] rounded-full mr-1"></span>
                                <span class="w-1.5 h-1.5 bg-[var(--ink-soft)] rounded-full mr-1"></span>
                                <span class="w-1.5 h-1.5 bg-[var(--ink-soft)] rounded-full"></span>
                            </div>
                        </div>

                        <div class="flex seq-2 chat-bubble">
                            <div>
                                <div class="bg-[var(--mint)] rounded-2xl rounded-tl-sm px-3.5 py-2 text-[13px] shadow-sm">
                                    Halo! Iya kak, buka tiap hari 08.00–21.00 😊
                                </div>
                                <span class="font-mono text-[9px] text-[var(--teal-deep)] ml-1">dibalas otomatis · AI</span>
                            </div>
                        </div>

                        <div class="flex justify-end seq-3 chat-bubble ml-auto">
                            <div class="bg-white rounded-2xl rounded-tr-sm px-3.5 py-2 text-[13px] shadow-sm">
                                Ada kopi susu gula aren ga kak?
                            </div>
                        </div>

                        <div class="flex typing-seq typing-seq-2">
                            <div class="bg-white rounded-2xl rounded-tl-sm px-3.5 py-2.5 shadow-sm typing-dots">
                                <span class="w-1.5 h-1.5 bg-[var(--ink-soft)] rounded-full mr-1"></span>
                                <span class="w-1.5 h-1.5 bg-[var(--ink-soft)] rounded-full mr-1"></span>
                                <span class="w-1.5 h-1.5 bg-[var(--ink-soft)] rounded-full"></span>
                            </div>
                        </div>

                        <div class="flex seq-4 chat-bubble">
                            <div>
                                <div class="bg-[var(--mint)] rounded-2xl rounded-tl-sm px-3.5 py-2 text-[13px] shadow-sm">
                                    Ada kak! Rp18.000, bisa dine-in atau take away 🙌
                                </div>
                                <span class="font-mono text-[9px] text-[var(--teal-deep)] ml-1">dibalas otomatis · AI</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SEBELUM / SESUDAH ===== --}}
    <section class="bg-white border-y border-black/5">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
            <h2 class="font-display font-bold text-[28px] sm:text-[34px] text-center max-w-2xl mx-auto leading-tight">
                Tiap chat yang telat dibalas, berpotensi jadi pelanggan yang kabur ke toko sebelah.
            </h2>
            <div class="grid sm:grid-cols-2 gap-6 mt-12">
                <div class="rounded-2xl border border-[var(--rust)]/20 bg-[var(--rust)]/[0.04] p-7">
                    <p class="font-mono text-[11px] uppercase tracking-wider text-[var(--rust)]">Tanpa AI</p>
                    <ul class="mt-4 space-y-3 text-[15px] text-[var(--ink-soft)]">
                        <li class="flex gap-2.5"><span class="text-[var(--rust)] mt-0.5">—</span> Pelanggan nunggu balasan berjam-jam, atau sampai besok</li>
                        <li class="flex gap-2.5"><span class="text-[var(--rust)] mt-0.5">—</span> Pertanyaan yang sama ditanya berulang kali, capek jawab manual</li>
                        <li class="flex gap-2.5"><span class="text-[var(--rust)] mt-0.5">—</span> Di luar jam kerja dan weekend, chat numpuk begitu saja</li>
                        <li class="flex gap-2.5"><span class="text-[var(--rust)] mt-0.5">—</span> Susah pantau performa CS kalau tim makin banyak</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-[var(--teal)]/25 bg-[var(--mint)] p-7">
                    <p class="font-mono text-[11px] uppercase tracking-wider text-[var(--teal-deep)]">Dengan {{ config('app.name', 'Balas') }}</p>
                    <ul class="mt-4 space-y-3 text-[15px] text-[var(--ink)]">
                        <li class="flex gap-2.5"><span class="text-[var(--teal-deep)] mt-0.5">✓</span> Balasan pertama dalam hitungan detik, 24 jam nonstop</li>
                        <li class="flex gap-2.5"><span class="text-[var(--teal-deep)] mt-0.5">✓</span> AI hafal semua FAQ & info produkmu, jawab konsisten tiap kali</li>
                        <li class="flex gap-2.5"><span class="text-[var(--teal-deep)] mt-0.5">✓</span> Kamu bisa ambil alih percakapan kapan saja dari satu inbox</li>
                        <li class="flex gap-2.5"><span class="text-[var(--teal-deep)] mt-0.5">✓</span> Tim agent bisa ditambah, kerjaan terbagi rapi</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CARA KERJA ===== --}}
    <section id="cara-kerja" class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
        <div class="max-w-xl">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[var(--teal-deep)]">Cara kerja</p>
            <h2 class="font-display font-bold text-[28px] sm:text-[34px] mt-2">Tiga langkah, online hari ini juga</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8 mt-12">
            <div>
                <span class="font-display text-[var(--teal)] text-[15px] font-bold">01</span>
                <h3 class="font-display font-semibold text-[17px] mt-2">Hubungkan WhatsApp</h3>
                <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-1.5">Scan satu kode QR dengan nomor WhatsApp bisnismu. Tidak perlu ganti nomor atau pakai WhatsApp Business API yang ribet.</p>
            </div>
            <div>
                <span class="font-display text-[var(--teal)] text-[15px] font-bold">02</span>
                <h3 class="font-display font-semibold text-[17px] mt-2">Ceritakan bisnismu</h3>
                <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-1.5">Isi info produk, harga, dan FAQ ke knowledge base. AI memakai ini sebagai satu-satunya sumber jawaban — tidak akan mengarang.</p>
            </div>
            <div>
                <span class="font-display text-[var(--teal)] text-[15px] font-bold">03</span>
                <h3 class="font-display font-semibold text-[17px] mt-2">AI mulai membalas</h3>
                <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-1.5">Pantau semua percakapan dari satu inbox. Matikan AI dan balas manual kapan pun kamu mau, per percakapan.</p>
            </div>
        </div>
    </section>

    {{-- ===== FITUR ===== --}}
    <section id="fitur" class="bg-[var(--ink)] text-white">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[var(--teal)]">Fitur</p>
            <h2 class="font-display font-bold text-[28px] sm:text-[34px] mt-2 max-w-lg">Semua yang dibutuhkan tim CS dalam satu tempat</h2>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-12">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-[var(--teal)]/20 flex items-center justify-center text-[var(--teal)] text-base">💬</div>
                    <h3 class="font-display font-semibold text-[15px] mt-3.5">Inbox Terpusat</h3>
                    <p class="text-white/60 text-[13px] leading-relaxed mt-1.5">Semua percakapan WhatsApp pelangganmu di satu layar, real-time.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-[var(--teal)]/20 flex items-center justify-center text-[var(--teal)] text-base">📚</div>
                    <h3 class="font-display font-semibold text-[15px] mt-3.5">Knowledge Base</h3>
                    <p class="text-white/60 text-[13px] leading-relaxed mt-1.5">AI menjawab berdasarkan info produk & SOP yang kamu tulis sendiri.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-[var(--teal)]/20 flex items-center justify-center text-[var(--teal)] text-base">🤖</div>
                    <h3 class="font-display font-semibold text-[15px] mt-3.5">Kepribadian AI</h3>
                    <p class="text-white/60 text-[13px] leading-relaxed mt-1.5">Atur gaya bicara — ramah atau formal — sesuai karakter brand-mu.</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
                    <div class="w-9 h-9 rounded-lg bg-[var(--teal)]/20 flex items-center justify-center text-[var(--teal)] text-base">👥</div>
                    <h3 class="font-display font-semibold text-[15px] mt-3.5">Ambil Alih Kapan Saja</h3>
                    <p class="text-white/60 text-[13px] leading-relaxed mt-1.5">Matikan AI per percakapan dan balas manual saat dibutuhkan tim.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== HARGA ===== --}}
    <section id="harga" class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
        <div class="text-center max-w-xl mx-auto">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[var(--teal-deep)]">Harga</p>
            <h2 class="font-display font-bold text-[28px] sm:text-[34px] mt-2">Mulai gratis, upgrade saat bisnismu tumbuh</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-12 items-start">
            <div class="rounded-2xl border border-black/10 bg-white p-7">
                <h3 class="font-display font-semibold text-[17px]">Starter</h3>
                <p class="text-[13px] text-[var(--ink-soft)] mt-1">Untuk bisnis yang baru mulai</p>
                <p class="font-display font-extrabold text-[34px] mt-5">Rp149rb<span class="text-[15px] font-medium text-[var(--ink-soft)]">/bulan</span></p>
                <ul class="text-[14px] text-[var(--ink-soft)] space-y-2.5 mt-6">
                    <li>✓ 1 nomor WhatsApp</li>
                    <li>✓ AI auto-reply tanpa batas</li>
                    <li>✓ 1 knowledge base</li>
                    <li>✓ 2 akun agent</li>
                </ul>
                <a href="{{ route('register') }}" class="block text-center mt-7 border border-black/10 hover:bg-black/[0.03] transition-colors font-medium text-[14px] py-2.5 rounded-xl">Mulai Gratis</a>
            </div>

            <div class="rounded-2xl border-2 border-[var(--teal)] bg-white p-7 relative shadow-[0_20px_45px_-25px_rgba(13,148,136,0.5)]">
                <span class="absolute -top-3 left-7 bg-[var(--teal)] text-white text-[11px] font-medium px-3 py-1 rounded-full">Paling populer</span>
                <h3 class="font-display font-semibold text-[17px]">Pro</h3>
                <p class="text-[13px] text-[var(--ink-soft)] mt-1">Untuk bisnis yang sedang tumbuh</p>
                <p class="font-display font-extrabold text-[34px] mt-5">Rp349rb<span class="text-[15px] font-medium text-[var(--ink-soft)]">/bulan</span></p>
                <ul class="text-[14px] text-[var(--ink-soft)] space-y-2.5 mt-6">
                    <li>✓ 3 nomor WhatsApp</li>
                    <li>✓ AI auto-reply tanpa batas</li>
                    <li>✓ Knowledge base tanpa batas</li>
                    <li>✓ 10 akun agent</li>
                    <li>✓ Laporan performa CS</li>
                </ul>
                <a href="{{ route('register') }}" class="block text-center mt-7 bg-[var(--teal)] hover:bg-[var(--teal-deep)] transition-colors text-white font-medium text-[14px] py-2.5 rounded-xl">Mulai Gratis</a>
            </div>

            <div class="rounded-2xl border border-black/10 bg-white p-7">
                <h3 class="font-display font-semibold text-[17px]">Enterprise</h3>
                <p class="text-[13px] text-[var(--ink-soft)] mt-1">Untuk tim besar & kebutuhan khusus</p>
                <p class="font-display font-extrabold text-[34px] mt-5">Custom</p>
                <ul class="text-[14px] text-[var(--ink-soft)] space-y-2.5 mt-6">
                    <li>✓ Nomor WhatsApp tanpa batas</li>
                    <li>✓ Integrasi & SLA khusus</li>
                    <li>✓ Onboarding & pelatihan tim</li>
                    <li>✓ Akun agent tanpa batas</li>
                </ul>
                <a href="mailto:halo@{{ str_replace(' ', '', strtolower(config('app.name', 'balas'))) }}.id" class="block text-center mt-7 border border-black/10 hover:bg-black/[0.03] transition-colors font-medium text-[14px] py-2.5 rounded-xl">Hubungi Kami</a>
            </div>
        </div>
        <p class="text-center text-xs text-[var(--ink-soft)] mt-6">Semua paket mulai dengan trial 14 hari penuh, tanpa kartu kredit.</p>
    </section>

    {{-- ===== FAQ ===== --}}
    <section id="faq" class="bg-white border-t border-black/5">
        <div class="max-w-3xl mx-auto px-5 sm:px-8 py-20">
            <p class="font-mono text-[11px] uppercase tracking-wider text-[var(--teal-deep)] text-center">FAQ</p>
            <h2 class="font-display font-bold text-[28px] sm:text-[34px] mt-2 text-center">Pertanyaan yang sering ditanyakan</h2>

            <div class="mt-10 divide-y divide-black/10 border-t border-b border-black/10">
                <details class="group py-5">
                    <summary class="flex justify-between items-center cursor-pointer font-display font-semibold text-[15px] list-none">
                        Apakah perlu WhatsApp Business API berbayar?
                        <span class="text-[var(--ink-soft)] group-open:rotate-45 transition-transform text-xl leading-none">+</span>
                    </summary>
                    <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-3">Tidak. Kamu cukup scan QR dengan WhatsApp biasa di HP-mu, mirip cara WhatsApp Web bekerja. Tidak ada biaya tambahan dari Meta.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex justify-between items-center cursor-pointer font-display font-semibold text-[15px] list-none">
                        Bagaimana kalau AI tidak tahu jawabannya?
                        <span class="text-[var(--ink-soft)] group-open:rotate-45 transition-transform text-xl leading-none">+</span>
                    </summary>
                    <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-3">AI hanya menjawab berdasarkan knowledge base yang kamu isi, dan tidak akan mengarang. Kalau di luar konteks itu, AI akan memberi pesan fallback yang bisa kamu atur, dan kamu bisa ambil alih percakapannya kapan saja.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex justify-between items-center cursor-pointer font-display font-semibold text-[15px] list-none">
                        Bisa pakai untuk lebih dari satu nomor WhatsApp?
                        <span class="text-[var(--ink-soft)] group-open:rotate-45 transition-transform text-xl leading-none">+</span>
                    </summary>
                    <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-3">Bisa, tergantung paket yang dipilih. Paket Pro mendukung sampai 3 nomor, dan Enterprise tanpa batas.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex justify-between items-center cursor-pointer font-display font-semibold text-[15px] list-none">
                        Apakah data pelanggan saya aman?
                        <span class="text-[var(--ink-soft)] group-open:rotate-45 transition-transform text-xl leading-none">+</span>
                    </summary>
                    <p class="text-[var(--ink-soft)] text-[14px] leading-relaxed mt-3">Setiap bisnis punya ruang data terpisah di sistem kami, dan tidak bisa diakses bisnis lain. Kamu juga bisa hapus data percakapan kapan saja.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- ===== CTA AKHIR ===== --}}
    <section class="max-w-6xl mx-auto px-5 sm:px-8 py-20">
        <div class="rounded-3xl bg-[var(--ink)] text-white px-8 py-14 text-center relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-[var(--teal)]/20 blur-3xl"></div>
            <h2 class="font-display font-bold text-[28px] sm:text-[36px] relative">Pelanggan kamu sedang chat sekarang juga.</h2>
            <p class="text-white/70 text-[15px] mt-3 relative">Hubungkan WhatsApp dalam 5 menit, dan mulai balas otomatis hari ini.</p>
            <a href="{{ route('register') }}" class="inline-block mt-7 bg-[var(--teal)] hover:bg-white hover:text-[var(--ink)] transition-colors text-white font-medium text-[15px] px-7 py-3 rounded-xl relative">
                Coba Gratis 14 Hari
            </a>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="border-t border-black/5">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-[var(--teal)] flex items-center justify-center text-white font-display font-bold text-xs">B</span>
                <span class="font-display font-semibold text-[14px]">{{ config('app.name', 'Balas') }}</span>
            </div>
            <p class="text-[13px] text-[var(--ink-soft)]">© {{ date('Y') }} {{ config('app.name', 'Balas') }}. Dibuat untuk pelaku bisnis Indonesia.</p>
        </div>
    </footer>

</body>
</html>
