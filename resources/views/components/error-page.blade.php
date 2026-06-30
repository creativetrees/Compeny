@props([
    'code' => '404',
    'title' => 'Something went wrong',
    'message' => '',
    'reason' => null,        // short status phrase; defaults to $title
    'eyebrow' => null,       // override eyebrow text
    'home' => true,          // show the default "Back home" action
])

@php
    $reason  = $reason ?: $title;
    $eyebrow = $eyebrow ?: 'Error · '.$reason;

    // request()->path() is user-controlled — Blade {{ }} escapes it. Trimmed so the
    // readout never overflows the card.
    $path  = '/'.ltrim(request()->path(), '/');
    $path  = \Illuminate\Support\Str::limit($path, 64);
    $stamp = now()->utc()->format('Y-m-d H:i').' UTC';

    $links = ['Work' => '/work', 'Services' => '/services', 'Pricing' => '/pricing', 'Contact' => '/contact'];
@endphp

<x-layouts.system :tag="'ERR://'.$code" :title="$code.' — '.$title">
    <div class="mb-7 flex justify-center sys-in" style="animation-delay: 0.05s">
        <x-ui.eyebrow>{{ $eyebrow }}</x-ui.eyebrow>
    </div>

    <h1 class="display sys-code sys-in" style="animation-delay: 0.1s">
        <span data-scramble data-scramble-duration="1100">{{ $code }}</span>
    </h1>

    <p class="display mx-auto mt-6 max-w-2xl text-[1.6rem] leading-[1.05] sys-in sm:text-[2rem] md:text-[2.4rem]" style="animation-delay: 0.22s">
        {{ $title }}
    </p>

    @if (filled($message))
        <p class="measure mx-auto mt-5 text-[1rem] leading-relaxed text-muted sys-in sm:text-[1.05rem]" style="animation-delay: 0.3s">
            {{ $message }}
        </p>
    @endif

    <div class="mt-9 flex flex-wrap items-center justify-center gap-3 sys-in" style="animation-delay: 0.38s">
        @isset($actions)
            {{ $actions }}
        @else
            @if ($home)
                <x-ui.button href="/">Back home</x-ui.button>
            @endif
            <x-ui.button href="/start" variant="ghost" :magnetic="false">Start a project</x-ui.button>
        @endisset
    </div>

    <dl class="mx-auto mt-12 w-full max-w-md border border-line bg-paper/60 text-left font-mono text-[0.72rem] sys-in" style="animation-delay: 0.46s">
        <div class="flex gap-4 border-b border-line px-4 py-2.5">
            <dt class="w-14 shrink-0 uppercase tracking-wide text-faint">Status</dt>
            <dd class="truncate uppercase tracking-wide text-ink">{{ $code }} · {{ $reason }}</dd>
        </div>
        <div class="flex gap-4 border-b border-line px-4 py-2.5">
            <dt class="w-14 shrink-0 uppercase tracking-wide text-faint">Path</dt>
            <dd class="truncate text-muted">{{ $path }}</dd>
        </div>
        <div class="flex gap-4 px-4 py-2.5">
            <dt class="w-14 shrink-0 uppercase tracking-wide text-faint">Time</dt>
            <dd class="truncate text-muted">{{ $stamp }}</dd>
        </div>
    </dl>

    <nav class="mt-10 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 sys-in" style="animation-delay: 0.54s" aria-label="Helpful links">
        @foreach ($links as $label => $url)
            <a href="{{ $url }}" class="link-underline font-mono text-[0.72rem] uppercase tracking-wide text-muted transition-colors hover:text-ink">{{ $label }}</a>
        @endforeach
    </nav>
</x-layouts.system>
