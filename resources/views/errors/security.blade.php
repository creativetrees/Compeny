@php
    // Rendered by App\Exceptions\RequestBlocked (with reason + ref) or directly.
    // Defaults keep it valid when rendered without context (e.g. preview).
    $reason = $reason ?? 'Suspicious request pattern';
    $ref    = $ref ?? ('CTG-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(4)).'-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(4)));
    $stamp  = now()->utc()->format('Y-m-d H:i').' UTC';
    $email  = $settings->contact_email ?? 'hello@creativetrees.group';
@endphp

<x-layouts.system :dark="true" tag="SEC://403" title="403 — Access blocked">
    <div class="mb-7 flex justify-center sys-in" style="animation-delay: 0.05s">
        <x-ui.eyebrow>Security · Request blocked</x-ui.eyebrow>
    </div>

    <h1 class="display sys-in text-[2.6rem] leading-[0.94] sm:text-[3.6rem] md:text-[5.2rem]" style="animation-delay: 0.1s">
        <span data-scramble data-scramble-duration="1100">ACCESS BLOCKED</span>
    </h1>

    <p class="measure mx-auto mt-6 text-[1rem] leading-relaxed text-[#b9b9b4] sys-in sm:text-[1.05rem]" style="animation-delay: 0.24s">
        This request was flagged by our security system and stopped. If you reached this by mistake, email us the reference below and we’ll clear it.
    </p>

    <div class="mt-9 flex flex-wrap items-center justify-center gap-3 sys-in" style="animation-delay: 0.34s">
        <x-ui.button href="mailto:{{ $email }}?subject=Security%20review%20{{ $ref }}" variant="invert">Email security</x-ui.button>
        <x-ui.button href="/" variant="ghost" :magnetic="false">Back home</x-ui.button>
    </div>

    <dl class="mx-auto mt-12 w-full max-w-md border border-white/15 text-left font-mono text-[0.72rem] sys-in" style="animation-delay: 0.44s">
        <div class="flex gap-4 border-b border-white/10 px-4 py-2.5">
            <dt class="w-16 shrink-0 uppercase tracking-wide text-[#7e7e7a]">Reason</dt>
            <dd class="truncate text-paper">{{ $reason }}</dd>
        </div>
        <div class="flex gap-4 border-b border-white/10 px-4 py-2.5">
            <dt class="w-16 shrink-0 uppercase tracking-wide text-[#7e7e7a]">Ref</dt>
            <dd class="truncate text-paper">{{ $ref }}</dd>
        </div>
        <div class="flex gap-4 px-4 py-2.5">
            <dt class="w-16 shrink-0 uppercase tracking-wide text-[#7e7e7a]">Time</dt>
            <dd class="truncate text-[#b9b9b4]">{{ $stamp }}</dd>
        </div>
    </dl>

    <p class="mt-8 font-mono text-[0.66rem] uppercase tracking-[0.18em] text-[#6f6f6c] sys-in" style="animation-delay: 0.5s">
        Incident logged · recorded for review
    </p>
</x-layouts.system>
