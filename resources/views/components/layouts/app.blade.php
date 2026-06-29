@props([
    'title' => null,
    'description' => null,
    'dark' => false,
])

@php
    $brand = $settings->brand_name ?? 'Creative Trees Group';
    $pageTitle = $title ? $title . ' — ' . $brand : ($settings->seo_title ?? $brand);
    $pageDescription = $description ?? $settings->seo_description ?? 'We design and build SaaS products that scale.';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="{{ $settings->favicon_url ?? '/favicon.svg' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    @if (filled($settings->seo_keywords ?? null))
        <meta name="keywords" content="{{ $settings->seo_keywords }}">
    @endif
    <meta name="robots" content="{{ ($settings->seo_noindex ?? false) ? 'noindex, nofollow' : 'index, follow' }}">

    {{-- Open Graph / Twitter --}}
    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @php
        $ogImage = $settings->seo_image_path ?? null;
        $ogImage = $ogImage ? (\Illuminate\Support\Str::startsWith($ogImage, 'http') ? $ogImage : url(\Illuminate\Support\Facades\Storage::url($ogImage))) : null;
    @endphp
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif

    {{-- Google Analytics (GA4) — id whitelisted to [A-Za-z0-9-], safe in URL & JS string contexts --}}
    @php
        $gaId = preg_replace('/[^A-Za-z0-9\-]/', '', (string) ($settings->google_analytics_id ?? ''));
    @endphp
    @if ($gaId !== '')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif

    @stack('head')

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,700,800|inter:400,500,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class(['surface-dark' => $dark])>
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:bg-ink focus:text-paper focus:px-4 focus:py-2 focus:rounded-full focus:font-mono focus:text-xs">
        Skip to content
    </a>

    <x-site.header />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site.footer />
</body>
</html>
