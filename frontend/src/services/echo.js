import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

let echoInstance = null;

export function getEcho() {
  if (!echoInstance) {
    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || 'eegqmq3jgc6lbtmgpy48';
    const reverbHost = import.meta.env.VITE_REVERB_HOST || 'localhost';
    const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

    try {
      echoInstance = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
      });
    } catch (e) {
      console.warn('Reverb WebSocket initialization deferred:', e);
    }
  }

  return echoInstance;
}
