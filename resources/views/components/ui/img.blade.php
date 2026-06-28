@props([
    'src' => null,
    'alt' => '',
    'widths' => [400, 800, 1200],
    'sizes' => '100vw',
    'loading' => 'lazy',
    'fetchpriority' => null,
])

@php
    // Local images live on the public disk (served at /storage/...) and get
    // responsive WebP variants via the /img resizer. Remote placeholder URLs
    // (picsum/pravatar) and external links are emitted unchanged.
    $isLocal = $src && \Illuminate\Support\Str::contains($src, '/storage/');
    $storagePath = $isLocal ? ltrim(\Illuminate\Support\Str::after($src, '/storage/'), '/') : null;

    $allowed = [400, 800, 1200, 1600];
    $useWidths = array_values(array_intersect($allowed, array_map('intval', (array) $widths))) ?: [800];

    $srcset = null;
    $fallback = $src;

    if ($storagePath !== null && $storagePath !== '') {
        $base = url('/img/'.$storagePath);
        $srcset = collect($useWidths)->map(fn ($w) => $base.'?w='.$w.' '.$w.'w')->implode(', ');
        $fallback = $base.'?w='.end($useWidths);
    }
@endphp

<img
    src="{{ $fallback }}"
    @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
    alt="{{ $alt }}"
    loading="{{ $loading }}"
    decoding="async"
    @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    {{ $attributes }}
>
