/* Laravel Echo + Reverb — real-time is OPT-IN.
   Default OFF (VITE_ENABLE_REALTIME != 'true') so nothing is attempted and the
   echo/pusher libs are code-split out of the default bundle (dynamic import). */
if (import.meta.env.VITE_ENABLE_REALTIME === 'true') {
    Promise.all([import('laravel-echo'), import('pusher-js')]).then(([{ default: Echo }, { default: Pusher }]) => {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
            wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    });
}
