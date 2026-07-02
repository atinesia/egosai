import './echo';   // setup Laravel Echo dengan Reverb
import Alpine from 'alpinejs';

// ── Alpine global store untuk notifikasi ─────────────────────────────────────
Alpine.store('notif', {
    items: [],
    unread: 0,

    add(payload) {
        // Tambah ke awal array, maks 30 item supaya tidak penuh terus
        this.items.unshift({ id: Date.now(), ...payload });
        if (this.items.length > 30) {
            this.items = this.items.slice(0, 30);
        }
        this.unread++;

        // Browser Notification — hanya kalau tab tidak aktif
        this.showBrowserNotif(payload);
    },

    markRead() {
        this.unread = 0;
    },

    clearAll() {
        this.items = [];
        this.unread = 0;
    },

    showBrowserNotif(payload) {
        // Kalau tab sedang aktif/fokus, tidak perlu browser notif
        if (document.hasFocus()) return;

        if (!('Notification' in window)) return;

        if (Notification.permission === 'granted') {
            this._sendNotif(payload);
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') this._sendNotif(payload);
            });
        }
    },

    _sendNotif(payload) {
        const notif = new Notification(payload.contact_name, {
            body: payload.message_preview,
            icon: '/favicon.ico',
            tag: 'wa-msg-' + payload.conversation_id,
            renotify: true,
        });

        // Klik notif → buka/fokus tab dan arahkan ke conversation
        notif.onclick = () => {
            window.focus();
            window.location.href = '/dashboard/inbox?conv=' + payload.conversation_id;
        };

        // Auto tutup setelah 6 detik
        setTimeout(() => notif.close(), 6000);
    },
});

Alpine.start();

// ── Subscribe ke channel Reverb ───────────────────────────────────────────────
const tenantId = window.__TENANT_ID__;

if (tenantId && window.Echo) {
    window.Echo
        .private(`tenant.${tenantId}`)
        .listen('.new-message', (payload) => {
            // Isi store Alpine — badge + dropdown akan otomatis reaktif
            Alpine.store('notif').add(payload);
        });
}

// Minta izin browser notification saat halaman pertama dibuka
// (sebelum ada pesan masuk, supaya tidak terlambat minta izin)
document.addEventListener('DOMContentLoaded', () => {
    if ('Notification' in window && Notification.permission === 'default') {
        // Tunda 3 detik agar tidak langsung muncul saat login
        setTimeout(() => Notification.requestPermission(), 3000);
    }
});
