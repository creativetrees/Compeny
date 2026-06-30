@props([
    'eyebrow' => null,
    'title' => null,
    'align' => 'left',
])

<div @class([
        'max-w-3xl',
        'mx-auto text-center' => $align === 'center',
    ]) data-reveal>
    @if ($eyebrow)
        <x-ui.eyebrow @class(['mb-5', 'justify-center' => $align === 'center']) data-scramble>{{ $eyebrow }}</x-ui.eyebrow>
    @endif

    @if ($title)
        <h2 class="display text-[2rem] leading-[1.02] sm:text-4xl md:text-5xl">{!! $title !!}</h2>
    @endif

    @if (! $slot->isEmpty())
        <div class="richtext mt-5 max-w-xl text-[0.97rem] text-muted @if ($align === 'center') mx-auto @endif">
            {{ $slot }}
        </div>
    @endif
</div>
