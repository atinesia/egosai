import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Laravel Reverb pakai protokol Pusher di balik layar,
// tapi kita arahkan ke server Reverb lokal, bukan Pusher cloud.
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    // Endpoint otorisasi private channel
    authEndpoint: '/broadcasting/auth',
});
