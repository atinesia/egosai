# AI Customer Service SaaS (mirip Cekat.ai)

Platform AI customer service multi-tenant berbasis WhatsApp, mirip cekat.ai.
Stack: **Laravel + Livewire** (dashboard & otak bisnis) + **Node.js (Baileys)**
(gateway WhatsApp) + **Groq AI** (otak balasan otomatis).

## Arsitektur

```
Pelanggan WA  ⇄  Node.js (Baileys gateway)  ⇄  Laravel (dashboard + AI)
                                                     ⇄  Groq API
```

1. Setiap tenant (bisnis) scan QR untuk menghubungkan nomor WhatsApp-nya
   lewat Node.js service (pakai library Baileys / WhatsApp Web multi-device).
2. Pesan masuk dari pelanggan → Node.js kirim webhook ke Laravel.
3. Laravel simpan pesan, cek apakah AI aktif untuk percakapan itu, lalu
   minta balasan ke **Groq** dengan context: prompt bisnis + knowledge base
   tenant + riwayat chat terakhir.
4. Balasan AI dikirim balik lewat Node.js ke pelanggan, dan tersimpan di
   inbox dashboard. Admin/agent bisa ambil alih kapan saja (toggle AI per
   percakapan) dan membalas manual dari Inbox.

Multi-tenant memakai pendekatan **single database + kolom `tenant_id`**
(lebih simpel untuk mulai). Semua model utama otomatis di-scope ke tenant
user yang login lewat trait `BelongsToTenant`. Kalau nanti butuh isolasi
lebih kuat (database per tenant), tinggal migrasi ke package
`stancl/tenancy`.

## Struktur folder

```
ai-cs-saas/
├── laravel-app/        # File aplikasi Laravel (app/, database/, resources/, routes/)
└── node-whatsapp-service/  # Microservice Node.js (Baileys) sebagai WhatsApp gateway
```

`laravel-app/` di sini **bukan** project Laravel yang lengkap (tidak ada
vendor/, bootstrap/, dll) — ini cuma file aplikasinya saja. Kamu perlu
membuat project Laravel baru lalu menyalin file-file ini ke dalamnya.
Sandbox saya tidak punya akses ke Packagist, jadi composer install harus
dijalankan di komputer/server kamu.

## Setup — Bagian Laravel

```bash
# 1. Buat project Laravel baru
composer create-project laravel/laravel ai-cs-app
cd ai-cs-app

# 2. Install Livewire
composer require livewire/livewire

# 3. Copy semua isi folder laravel-app/ dari hasil saya ke project ini
#    (timpa app/, database/migrations/, resources/views/, routes/web.php, routes/api.php)

# 4. Tambahkan isi config/services.snippet.php ke config/services.php kamu
#    (tinggal copy array 'groq' dan 'whatsapp_node' ke dalam array return [...])

# 5. Tambahkan isi .env.additions.example ke file .env kamu, isi GROQ_API_KEY
#    (daftar gratis di https://console.groq.com)

# 6. Daftarkan middleware alias 'ensure.tenant' di bootstrap/app.php:
```

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'ensure.tenant' => \App\Http\Middleware\EnsureTenant::class,
        'ensure.onboarded' => \App\Http\Middleware\EnsureOnboarded::class,
    ]);
})
```

```bash
# 7. Install Tailwind CSS
#    Laravel versi terbaru biasanya sudah include Tailwind v4 lewat
#    @tailwindcss/vite plugin. Cek dulu versi yang ke-install:
cat package.json | grep tailwindcss
```

**Kalau dapat Tailwind v4** (`"tailwindcss": "^4.x"` dan ada
`@tailwindcss/vite` di devDependencies) — kamu tidak perlu konfigurasi
tambahan, `resources/css/app.css` yang saya buat sudah pakai sintaks v4
(`@import "tailwindcss";`, `@theme`, `@source`). Pastikan `vite.config.js`
sudah include plugin-nya:

```js
// vite.config.js
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
        tailwindcss(),
    ],
});
```

**Kalau dapat Tailwind v3** (`"tailwindcss": "^3.x"`) — ganti isi
`resources/css/app.css` ke sintaks v3 (hapus blok `@theme` dan baris
`@source`, ganti baris pertama jadi 3 baris
`@tailwind base; @tailwind components; @tailwind utilities;`), lalu:

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
# tailwind.config.js yang saya buat sudah siap pakai untuk v3
```

> Error `Cannot apply unknown utility class ... missing @reference` saat
> `npm run dev`/`build` artinya project kamu pakai Tailwind v4 tapi
> `app.css` masih ditulis dengan sintaks v3 lama, atau sebaliknya.
> Cocokkan versi `tailwindcss` di `package.json` dengan sintaks `app.css`
> sesuai panduan di atas.

```bash
# 8. Jalankan migrasi
php artisan migrate

# 9. Jalankan dev server
npm run dev
php artisan serve
```

Buka `http://localhost:8000/register` untuk membuat workspace (tenant)
pertama kamu.

## Setup — Bagian Node.js (WhatsApp gateway)

```bash
cd node-whatsapp-service
npm install
cp .env.example .env
# isi LARAVEL_BASE_URL dan WEBHOOK_SECRET (HARUS SAMA dengan WHATSAPP_WEBHOOK_SECRET di Laravel)
npm start
```

Service ini jalan di port `3001` secara default dan menyediakan endpoint
internal yang dipanggil Laravel (`/sessions/:id/start`, `/send`, `/logout`).
Ia juga mengirim webhook balik ke Laravel saat ada QR baru, status koneksi
berubah, atau pesan masuk.

Setelah Laravel & Node jalan, masuk ke menu **Koneksi WhatsApp** di
dashboard → klik "Hubungkan WhatsApp" → scan QR yang muncul dengan HP yang
mau dipakai sebagai nomor customer service.

## Onboarding

Setelah daftar, tenant baru diarahkan ke wizard 4 langkah di `/onboarding`
sebelum bisa masuk ke dashboard (dipaksa lewat middleware `ensure.onboarded`):

1. **Tentang Bisnis** — nama, kategori, deskripsi singkat (jadi context AI)
2. **Kepribadian AI** — pilih gaya bicara (ramah/formal), prompt otomatis
   ter-generate dan bisa diedit bebas
3. **Knowledge Base awal** — isi sampai 3 FAQ cepat (opsional, bisa dilewati)
4. **Hubungkan WhatsApp** — scan QR, atau lewati dan sambungkan nanti dari
   menu Dashboard

Tenant ditandai `onboarding_completed_at` setelah klik selesai di step 4
(atau klik "Lewati"). Semua data step sebelumnya sudah tersimpan permanen
ke database meski user belum klik "Selesai" di step terakhir.

## Landing page publik

Halaman utama (`/`) sekarang menampilkan landing page marketing, bukan
langsung redirect ke login. Filenya di `resources/views/landing.blade.php`
(satu file penuh, bukan komponen Livewire — murni statis + Tailwind).

Isinya: hero dengan mockup chat WhatsApp animasi CSS (loop otomatis
menunjukkan AI membalas pelanggan), perbandingan sebelum/sesudah pakai AI,
3 langkah cara kerja, grid fitur, 3 tier harga (Starter/Pro/Enterprise),
FAQ accordion, dan CTA penutup. Semua teks pakai Bahasa Indonesia dan
contoh kasus UMKM (kedai kopi) supaya relevan dengan audiens.

Ganti nama bisnis/produk dengan ubah `APP_NAME` di `.env` — landing page
otomatis menyesuaikan lewat `config('app.name')`. Harga di halaman ini
masih contoh statis; kalau nanti sudah ada sistem billing, sambungkan ke
data `plan` di model `Tenant`.

## Perbaikan UI: loading state & spacing form

Dua perbaikan diterapkan ke seluruh aplikasi (bukan cuma satu halaman):

**Loading state di setiap tombol interaktif** — semua tombol yang memicu
aksi Livewire (submit form, toggle AI, hapus, simpan, hubungkan WhatsApp,
dst) sekarang punya state visual saat sedang diproses: teks berubah
sementara + spinner kecil muncul, dan tombol otomatis ter-disable supaya
tidak ke-klik dua kali. Dipakai lewat kombinasi `wire:loading`,
`wire:loading.attr="disabled"`, dan `wire:target` di tiap tombol, dengan
dua class utility baru di `resources/css/app.css`: `.btn-spinner` (untuk
tombol warna gelap/teal) dan `.btn-spinner-dark` (untuk tombol/badge warna
terang).

**Spacing input/textarea/select** — sebelumnya banyak field cuma pakai
`rounded-lg border-slate-300` tanpa padding eksplisit, jadi teks terasa
mepet ke tepi. Sekarang ada rule global di `resources/css/app.css` (layer
`base`) yang otomatis kasih `px-3.5 py-2.5` ke semua `input[type=...]`,
`textarea`, dan `select` di seluruh aplikasi — tidak perlu nulis padding
manual lagi tiap bikin field baru, supaya konsisten.

**Catatan penting**: layout (`app.blade.php`, `guest.blade.php`,
`onboarding.blade.php`) sebelumnya belum punya `@livewireStyles` /
`@livewireScripts` — ini sudah ditambahkan. Tanpa direktif ini, Livewire
(dan Alpine.js bawaannya yang dipakai di tombol logout) tidak akan
berfungsi sama sekali. Pastikan juga `tailwind.config.js` (sudah
disertakan) men-scan folder `app/Livewire/**/*.php` supaya class yang
ditulis di `render()` ikut ter-compile.

## Alur kerja AI

- **Pengaturan AI** (`/dashboard/ai-settings`): atur model Groq, prompt
  kepribadian, temperature, dan pesan fallback per tenant.
- **Knowledge Base** (`/dashboard/knowledge-base`): isi FAQ/SOP/info produk.
  Semua entri aktif otomatis disuntikkan sebagai context ke setiap request
  ke Groq, supaya AI menjawab sesuai bisnis kamu, bukan ngarang.
- **Inbox** (`/dashboard/inbox`): lihat semua percakapan real-time
  (auto-refresh tiap 5 detik), toggle AI on/off per percakapan, atau balas
  manual — pesan manual akan langsung dikirim lewat WhatsApp juga.

## Troubleshooting: QR WhatsApp tidak muncul / tombol balik lagi

Kalau klik "Hubungkan WhatsApp" dan tampilan cuma kembali ke tombol semula
(tanpa pesan error), cek dalam urutan ini:

1. **Routes API belum terdaftar.** Di Laravel 11+, `routes/api.php` TIDAK
   otomatis di-load — harus didaftarkan manual. Cek `bootstrap/app.php`:

   ```php
   ->withRouting(
       web: __DIR__.'/../routes/web.php',
       api: __DIR__.'/../routes/api.php', // <- pastikan baris ini ada
       commands: __DIR__.'/../routes/console.php',
       health: '/up',
   )
   ```

   Kalau belum ada, jalankan `php artisan install:api` atau tambahkan baris
   `api:` di atas secara manual. Tanpa ini, webhook dari Node *tidak akan
   pernah sampai* ke Laravel meskipun Node service-nya jalan normal.

2. **Node service belum dijalankan**, atau `WHATSAPP_NODE_URL` di `.env`
   Laravel salah (default `http://localhost:3001`). Sejak update terakhir,
   kalau Laravel gagal menghubungi Node, halaman akan menampilkan banner
   merah dengan pesan error spesifik — kalau banner itu tidak muncul sama
   sekali padahal masih gagal, berarti kamu pakai versi kode lama (lihat
   poin di bawah).

3. **`WHATSAPP_WEBHOOK_SECRET` di `.env` Laravel beda dengan
   `WEBHOOK_SECRET` di `.env` Node service** — keduanya harus sama persis.
   Kalau beda, Node akan menolak request dari Laravel dengan 401 (akan
   muncul di banner error sebagai "Secret tidak cocok...").

4. Cek terminal tempat `npm start` dijalankan — kalau ada error di sana
   (port dipakai, gagal generate QR, dll), itu sumbernya bukan dari sisi
   Laravel.

5. Buka tab Network di browser saat klik tombol, cari request ke
   `livewire/update` — kalau responsnya 500, buka `storage/logs/laravel.log`
   untuk detail errornya.

`WhatsappService` sekarang tidak pernah melempar exception mentah ke
Livewire — semua kegagalan (Node down, secret salah, timeout) ditangkap
dan ditampilkan sebagai pesan error yang jelas di halaman, baik di
`/dashboard/whatsapp` maupun step 4 onboarding.

## Troubleshooting: balasan tidak terkirim / nomor tujuan salah

Akar masalahnya: WhatsApp punya skema privasi nomor ("LID") di mana JID
pengirim pesan bisa berupa ID internal seperti `123456789012345@lid`,
**bukan** nomor telepon asli (`62812xxxx@s.whatsapp.net`). Kode versi
sebelumnya men-strip JID jadi angka saja lalu merekonstruksi ulang
`{angka}@s.whatsapp.net` saat mau membalas — kalau JID aslinya bukan
`@s.whatsapp.net` (atau angkanya memang bukan representasi nomor asli),
hasil rekonstruksinya salah dan kirim pesan gagal/salah sasaran.

Perbaikannya: `wa_number` di database sekarang menyimpan **JID lengkap
apa adanya** dari Baileys (dengan domainnya, `@s.whatsapp.net` atau
`@lid`), dan dipakai persis itu saat mengirim balasan — tidak ada lagi
proses tebak-ulang. Untuk tampilan di dashboard (Inbox, Kontak), dipakai
accessor `Contact::display_number` yang membuang akhiran domain supaya
tetap enak dibaca; kontak ber-JID `@lid` ditampilkan sebagai "Kontak
WhatsApp (privasi nomor aktif)" karena nomor aslinya memang tidak selalu
bisa didapat dari WhatsApp.

**Penting — bersihkan data lama**: kalau kamu sempat testing sebelum
perbaikan ini, tabel `contacts` mungkin berisi baris dengan `wa_number`
berupa angka polos tanpa domain (hasil dari kode lama yang salah). Baris
itu tidak akan otomatis kepakai untuk JID `@lid`, jadi hapus saja dan
biarkan contact baru terbuat otomatis dari pesan masuk berikutnya:

```bash
php artisan tinker
>>> App\Models\Contact::withoutGlobalScopes()->where('wa_number', 'not like', '%@%')->delete()
# atau lebih aman, cek dulu sebelum hapus:
>>> App\Models\Contact::withoutGlobalScopes()->get(['id','wa_number'])
```

## Model Groq yang disarankan

| Model | Kelebihan |
|---|---|
| `llama-3.3-70b-versatile` | Kualitas jawaban terbaik, cocok produksi |
| `llama-3.1-8b-instant` | Sangat cepat & murah, cocok volume tinggi |
| `mixtral-8x7b-32768` | Context window besar |

## Yang masih perlu dikembangkan (roadmap)

- Billing/subscription per plan (trial/starter/pro)
- Manajemen multi-agent + assignment otomatis
- Laravel Echo / Reverb untuk real-time (saat ini pakai polling sederhana)
- Dukungan media (gambar/dokumen) di pesan WhatsApp, saat ini hanya teks
- Multi-database tenancy (stancl/tenancy) kalau sudah scale besar
- Rate limiting & queue (Laravel queue) untuk panggilan ke Groq agar tidak
  blocking request webhook saat traffic tinggi
