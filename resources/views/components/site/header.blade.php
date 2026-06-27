@php
    $nav = \App\Models\NavLink::query()->where('location', 'header')->ordered()->get();
@endphp

<header
    x-data="{ scrolled: false, open: false }"
    @scroll.window="scrolled = window.scrollY > 24"
    :class="scrolled ? 'border-line/100 bg-paper/85 backdrop-blur' : 'border-transparent bg-paper/0'"
    class="fixed inset-x-0 top-0 z-50 border-b transition-colors duration-500"
>
    <div class="frame !border-0 flex h-[68px] items-center justify-between gap-6">
        {{-- Logo --}}
        <a href="/" class="group inline-flex items-center gap-2.5" aria-label="{{ $settings->brand_name ?? 'Creative Trees Group' }} — home">
            <x-ui.logo-mark class="h-5 w-5 text-ink transition-transform duration-500 group-hover:rotate-90" />
            <span class="font-mono text-[0.92rem] font-bold uppercase tracking-tight">Creative&nbsp;Trees</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-7 lg:flex">
            @foreach ($nav as $item)
                <a href="{{ $item->url }}"
                   @class([
                       'link-underline font-mono text-[0.78rem] uppercase tracking-wide text-ink/80 transition-colors hover:text-ink',
                   ])
                   @if (request()->is(ltrim($item->url, '/').'*')) aria-current="page" @endif
                >{{ $item->label }}</a>
            @endforeach
        </nav>

        {{-- CTA + mobile toggle --}}
        <div class="flex items-center gap-3">
            <a href="/start" data-magnetic class="hidden btn sm:inline-flex">Start a project</a>

            <button
                @click="open = true"
                class="inline-flex h-10 w-10 items-center justify-center lg:hidden"
                aria-label="Open menu"
                aria-controls="mobile-menu"
                :aria-expanded="open"
            >
                <span class="relative block h-3 w-5">
                    <span class="absolute left-0 top-0 h-px w-full bg-ink"></span>
                    <span class="absolute left-0 top-1.5 h-px w-full bg-ink"></span>
                    <span class="absolute bottom-0 left-0 h-px w-full bg-ink"></span>
                </span>
            </button>
        </div>
    </div>

    {{-- Mobile full-screen menu --}}
    <div
        x-show="open"
        x-cloak
        id="mobile-menu"
        role="dialog"
        aria-modal="true"
        aria-label="Site menu"
        x-trap.noscroll="open"
        x-transition:enter="transition duration-400 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-300 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex flex-col bg-paper lg:hidden"
        @keydown.escape.window="open = false"
    >
        <div class="frame !border-0 flex h-[68px] items-center justify-between">
            <span class="font-mono text-[0.92rem] font-bold uppercase tracking-tight">Menu</span>
            <button @click="open = false" class="font-mono text-xs uppercase tracking-widest" aria-label="Close menu">Close ✕</button>
        </div>

        <nav class="frame !border-0 mt-6 flex flex-1 flex-col gap-1">
            @foreach ($nav as $i => $item)
                <a href="{{ $item->url }}"
                   class="display flex items-baseline gap-4 border-b border-line py-5 text-3xl"
                   @click="open = false">
                    <span class="label-mono text-faint">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    {{ $item->label }}
                </a>
            @endforeach
            <a href="/start" class="btn mt-8 self-start" @click="open = false">Start a project</a>
        </nav>
    </div>
</header>
