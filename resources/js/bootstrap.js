import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbEnabled = String(import.meta.env.VITE_REVERB_ENABLED || '').toLowerCase() === 'true';

if (reverbKey && reverbEnabled) {
    const scheme = import.meta.env.VITE_REVERB_SCHEME || window.location.protocol.replace(':', '');
    const forceTLS = scheme === 'https';
    const rawHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
    const url = new URL(/^https?:\/\//i.test(rawHost) ? rawHost : `${scheme}://${rawHost}`);
    const port = Number(import.meta.env.VITE_REVERB_PORT || url.port || (forceTLS ? 443 : 80));

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: url.hostname,
        wsPort: port,
        wssPort: port,
        forceTLS,
        enabledTransports: ['ws', 'wss'],
    });
}
