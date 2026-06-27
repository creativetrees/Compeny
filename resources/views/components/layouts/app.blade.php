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
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="canonical" href="{{ url()->current() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">

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

    @stack('head')

    <link rel="preconnect" href="https://fonts.bunny.net">
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
