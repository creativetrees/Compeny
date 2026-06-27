@props([
    'value' => '',
    'label' => '',
    'divider' => false,
])

<div data-stagger-item @class(['lg:border-l lg:border-white/10 lg:pl-10' => $divider])>
    <div class="display text-5xl tabular-nums sm:text-6xl" data-count>{{ $value }}</div>
    <div class="label-mono mt-3">{{ $label }}</div>
</div>
