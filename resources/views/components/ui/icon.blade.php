@props([
    'name' => 'heroicon-o-sparkles',
    'class' => 'h-5 w-5',
])

@php
    // Render the heroicon, falling back gracefully if an unknown name was entered in admin.
    try {
        $html = svg($name ?: 'heroicon-o-sparkles', $class)->toHtml();
    } catch (\Throwable $e) {
        $html = svg('heroicon-o-sparkles', $class)->toHtml();
    }
@endphp

{!! $html !!}
