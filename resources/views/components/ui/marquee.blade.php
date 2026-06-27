@props([
    'clients' => collect(),
])

@php
    $clients = collect($clients)->filter();
    $count = $clients->count();

    // Build one "lane" wide enough to cover wide screens, then render it twice and
    // translate -50% → a perfectly seamless, gap-free loop on any viewport.
    $reps = $count ? max(2, (int) ceil(2200 / ($count * 160))) : 1;
    $lane = collect();
    for ($r = 0; $r < $reps; $r++) {
        $lane = $lane->concat($clients);
    }
    // Constant scroll speed regardless of item count (~2.4s per item).
    $duration = max(18, (int) round($lane->count() * 2.4));
    $track = $lane->concat($lane);
@endphp

@if ($count)
    <div class="marquee-viewport" style="--marquee-duration: {{ $duration }}s;">
        <div class="marquee">
            @foreach ($track as $client)
                @php
                    $href = filled($client->website_url) ? $client->website_url : null;
                    $classes = 'marquee-item'.($href ? '' : ' marquee-item--static');
                    $label = $client->name.($href ? ' — visit website' : '');
                @endphp
                <a
                    @if ($href) href="{{ $href }}" target="_blank" rel="noopener noreferrer" @endif
                    class="{{ $classes }}"
                    aria-label="{{ $label }}"
                >
                    @if ($client->logo_url)
                        <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" loading="lazy"
                             class="h-5 w-auto max-w-[140px] object-contain">
                    @else
                        <x-ui.logo-mark class="h-3.5 w-3.5 shrink-0" />
                        <span>{{ $client->name }}</span>
                    @endif

                    @if ($href)
                        <span class="marquee-tip" aria-hidden="true">{{ $client->name }} ↗</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
