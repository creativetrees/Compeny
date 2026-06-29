@php
    $nav = \App\Models\NavLink::query()->where('location', 'header')->ordered()->get();
@endphp

<header
    x-data="siteHeader"
    @scroll.window="onScroll()"
    @resize.window.debounce="onResize()"
    :class="headerClass"
    class="fixed inset-x-0 top-0 z-50 border-b transition-colors duration-500"
>
    <div class="frame !border-0 flex h-[68px] items-center justify-between gap-6">
        {{-- Logo --}}
        <a href="/" class="group inline-flex items-center gap-2.5" aria-label="{{ $settings->brand_name ?? 'Creative Trees Group' }} — home">
            @if ($settings->logo_url)
                <img src="{{ $settings->logo_url }}" alt="{{ $settings->brand_name ?? 'Creative Trees Group' }}" class="h-8 w-auto">
            @else
                <x-ui.logo-mark class="h-7 w-7 text-ink transition-transform duration-500 group-hover:rotate-90" />
            @endif
            @if (filled($settings->logo_text ?: $settings->brand_name))
                <span class="font-mono text-[0.95rem] font-bold uppercase tracking-tight">{{ $settings->logo_text ?: ($settings->brand_name ?? 'Creative Trees Group') }}</span>
            @endif
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-7 lg:flex" aria-label="Primary">
            @foreach ($nav as $item)
                <a href="{{ $item->url }}"
                   @class([
                       'link-underline font-mono text-[0.78rem] uppercase tracking-wide text-ink/80 transition-colors hover:text-ink',
                   ])
                   @if ($item->url === '/' ? request()->is('/') : request()->is(ltrim($item->url, '/').'*')) aria-current="page" @endif
                >{{ $item->label }}</a>
            @endforeach
        </nav>

        {{-- CTA + mobile toggle --}}
        <div class="flex items-center gap-3">
            <a href="/start" data-magnetic class="hidden btn sm:inline-flex">Start a project</a>

            <button
                @click="openMenu()"
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
        class="menu-panel fixed inset-0 z-50 flex flex-col bg-paper lg:hidden"
        @keydown.escape.window="closeMenu()"
    >
        <div class="frame !border-0 flex h-[68px] items-center justify-between">
            <span class="font-mono text-[0.92rem] font-bold uppercase tracking-tight">Menu</span>
            <button @click="closeMenu()" class="font-mono text-xs uppercase tracking-widest" aria-label="Close menu">Close ✕</button>
        </div>

        <nav class="frame !border-0 mt-6 flex flex-1 flex-col gap-1" aria-label="Mobile">
            @foreach ($nav as $i => $item)
                <a href="{{ $item->url }}"
                   class="menu-link display flex items-baseline gap-4 border-b border-line py-5 text-3xl"
                   style="animation-delay: {{ 80 + $i * 55 }}ms"
                   @click="closeMenu()">
                    <span class="label-mono text-faint">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    {{ $item->label }}
                </a>
            @endforeach
            <a href="/start" class="menu-link btn mt-8 self-start" style="animation-delay: {{ 80 + $nav->count() * 55 }}ms" @click="closeMenu()">Start a project</a>
        </nav>
    </div>
</header>
