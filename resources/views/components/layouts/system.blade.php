@props([
    'dark' => false,
    'tag' => null,        // top-right mono tag, e.g. ERR://404
    'title' => 'System',  // <title> prefix (brand appended)
])

@php
    // Self-contained chrome shared by every system page (errors, security,
    // maintenance). DB-free except $settings (guarded) so it renders during a
    // 500 / DB outage. Nav is intentionally minimal + static.
    $brand    = $settings->brand_name ?? 'Creative Trees Group';
    $logoText = ($settings->logo_text ?? null) ?: $brand;
    $logoUrl  = $settings->logo_url ?? null;
    $location = ($settings->footer_location ?? null) ?: 'Jakarta · Remote-first';
    $borderC  = $dark ? 'border-white/10' : 'border-line';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $dark ? '#0a0a0a' : '#ffffff' }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ $settings->favicon_url ?? '/favicon.svg' }}">

    <title>{{ $title }} · {{ $brand }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,700,800|inter:400,500,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Crash-safe entrance: pure-CSS, end-state visible (works without JS/assets). --}}
    <style>
        @keyframes sys-in {
            from { opacity: 0; transform: translateY(16px); filter: blur(6px); }
            to   { opacity: 1; transform: none; filter: none; }
        }
        .sys-in { animation: sys-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .sys-code { font-size: clamp(5.5rem, 30vw, 22rem); line-height: 0.84; }
        @media (prefers-reduced-motion: reduce) { .sys-in { animation: none; } }
    </style>
    {{ $head ?? '' }}
</head>
<body @class(['flex min-h-screen flex-col', 'surface-dark' => $dark])>
    <header class="frame !border-0 flex h-[68px] shrink-0 items-center justify-between gap-6">
        <a href="/" class="group inline-flex items-center gap-2.5" aria-label="{{ $brand }} — home">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="h-8 w-auto">
            @else
                <x-ui.logo-mark class="h-7 w-7 {{ $dark ? 'text-paper' : 'text-ink' }} transition-transform duration-500 group-hover:rotate-90" />
            @endif
            <span class="font-mono text-[0.95rem] font-bold uppercase tracking-tight">{{ $logoText }}</span>
        </a>
        @if ($tag)
            <span class="label-mono hidden sm:inline">{{ $tag }}</span>
        @endif
    </header>

    <main id="main" class="relative flex flex-1 items-center overflow-hidden border-y {{ $borderC }}">
        <div data-charfield class="charfield" aria-hidden="true"></div>

        <span class="tick left-6 top-6 md:left-10 md:top-10"></span>
        <span class="tick right-6 top-6 md:right-10 md:top-10"></span>
        <span class="tick bottom-6 left-6 md:bottom-10 md:left-10"></span>
        <span class="tick bottom-6 right-6 md:bottom-10 md:right-10"></span>

        <div class="frame !border-0 relative w-full py-20 text-center md:py-28">
            {{ $slot }}
        </div>
    </main>

    <footer class="frame !border-0 flex shrink-0 flex-col gap-1 py-6 font-mono text-[0.72rem] uppercase tracking-wide {{ $dark ? 'text-[#8a8a86]' : 'text-faint' }} sm:flex-row sm:items-center sm:justify-between">
        <span>© {{ date('Y') }} {{ $brand }}</span>
        <span>{{ $location }}</span>
    </footer>
</body>
</html>
