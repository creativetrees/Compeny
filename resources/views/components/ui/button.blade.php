@props([
    'href' => null,
    'variant' => 'solid',
    'magnetic' => true,
])

@php
    $classes = 'btn' . match ($variant) {
        'ghost' => ' btn--ghost',
        'invert' => ' btn--invert',
        default => '',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" @if ($magnetic) data-magnetic @endif {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button @if ($magnetic) data-magnetic @endif {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
