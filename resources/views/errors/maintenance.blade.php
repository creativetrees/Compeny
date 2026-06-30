@php
    // Shown by: php artisan down --render="errors::maintenance"
    $email = $settings->contact_email ?? 'hello@creativetrees.group';
    $tz    = now()->format('T');
    // Maintenance window. Start = now (when down was run); End = +20 min (adjust to
    // your real window). Full localised date, e.g. "Senin, 29 Juni 2026 - 23:16".
    $start = now()->locale('id')->translatedFormat('l, j F Y - H:i').' '.$tz;
    $end   = now()->addMinutes(20)->locale('id')->translatedFormat('l, j F Y - H:i').' '.$tz;
@endphp

<x-layouts.system tag="SYS://STATUS" title="Scheduled maintenance">
    <x-slot:head>
        <style>
            .sys-bar {
                position: relative; height: 3px; width: 100%; max-width: 26rem;
                margin-inline: auto; overflow: hidden; background: var(--color-line);
            }
            .sys-bar::after {
                content: ''; position: absolute; inset: 0; width: 38%;
                background: var(--color-ink);
                animation: sys-bar 1.8s cubic-bezier(0.65, 0, 0.35, 1) infinite;
            }
            @keyframes sys-bar { 0% { transform: translateX(-110%); } 100% { transform: translateX(380%); } }
            @media (prefers-reduced-motion: reduce) { .sys-bar::after { animation: none; width: 55%; } }
        </style>
    </x-slot:head>

    <div class="mb-7 flex justify-center sys-in" style="animation-delay: 0.05s">
        <x-ui.eyebrow>System status · Scheduled maintenance</x-ui.eyebrow>
    </div>

    <h1 class="display sys-in text-[2.5rem] leading-[0.95] sm:text-5xl md:text-[5rem]" style="animation-delay: 0.1s">
        <span data-scramble data-scramble-duration="1000">{{ content('system.maint_title', 'WE’LL BE RIGHT BACK') }}</span>
    </h1>

    <p class="measure mx-auto mt-6 text-[1rem] leading-relaxed text-muted sys-in sm:text-[1.05rem]" style="animation-delay: 0.24s">
        {{ content('system.maint_message', 'We’re shipping a quick upgrade, so the site is briefly offline. No action needed — it’ll be back to normal shortly.') }}
    </p>

    <div class="mt-9 sys-in" style="animation-delay: 0.32s">
        <div class="sys-bar" role="progressbar" aria-label="Maintenance in progress" aria-valuetext="Working"></div>
    </div>

    {{-- Maintenance window — WINDOW on top, then START / END (full localised dates). --}}
    <table class="mx-auto mt-10 w-full max-w-md border-collapse border border-line bg-paper/60 text-left font-mono text-[0.72rem] sys-in" style="animation-delay: 0.42s">
        <tbody>
            <tr class="border-b border-line">
                <th scope="row" class="w-24 px-4 py-2.5 text-left align-top font-medium uppercase tracking-wide text-faint">Window</th>
                <td class="px-4 py-2.5 text-ink">Scheduled maintenance</td>
            </tr>
            <tr class="border-b border-line">
                <th scope="row" class="px-4 py-2.5 text-left align-top font-medium uppercase tracking-wide text-faint">Start</th>
                <td class="px-4 py-2.5 text-muted">{{ $start }}</td>
            </tr>
            <tr>
                <th scope="row" class="px-4 py-2.5 text-left align-top font-medium uppercase tracking-wide text-faint">End</th>
                <td class="px-4 py-2.5 text-muted">{{ $end }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-9 flex flex-wrap items-center justify-center gap-3 sys-in" style="animation-delay: 0.5s">
        <x-ui.button href="mailto:{{ $email }}">Email us</x-ui.button>
        <x-ui.button href="/" variant="ghost" :magnetic="false">Try again</x-ui.button>
    </div>
</x-layouts.system>
