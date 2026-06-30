/**
 * CATATAN: Project ini didesain untuk Tailwind CSS v4.
 *
 * Di Tailwind v4, konfigurasi tema (font, warna custom, dll) sudah
 * dipindah ke dalam file resources/css/app.css lewat blok @theme,
 * dan path yang perlu di-scan untuk class diatur lewat @source.
 * File tailwind.config.js ini TIDAK WAJIB ada lagi di v4.
 *
 * File ini cuma disertakan untuk jaga-jaga kalau project Laravel kamu
 * masih pakai Tailwind v3 (instalasi lama). Kalau iya, hapus baris
 * `@theme { ... }` dan `@source "..."` di app.css, lalu kembalikan
 * app.css ke 3 baris `@tailwind base/components/utilities;` versi v3.
 */

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Inter"', 'system-ui', 'sans-serif'],
                display: ['"Plus Jakarta Sans"', '"Inter"', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
