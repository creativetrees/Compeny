@props([
    'name' => 'heroicon-o-sparkles',
    'class' => 'h-5 w-5',
])

@php
    // Harden the shared icon sink: the SVG is emitted with {!! !!}, and the
    // underlying blade-icons library does NOT html-escape attribute values, so a
    // dynamic class/name must never be able to break out of the markup.
    //  - $name (admin-controlled via $service->icon) is constrained to a plain icon
    //    identifier — blocks path/markup tricks; an unknown name falls back below.
    //  - $class is html-escaped so a stray quote can't inject attributes; legit
    //    Tailwind classes (incl. arbitrary values) decode back to the same string.
    $safeName = preg_match('/^[A-Za-z0-9._:-]+$/', (string) $name) ? $name : 'heroicon-o-sparkles';
    $safeClass = e($class);

    // Render the heroicon, falling back gracefully if an unknown name was entered in admin.
    try {
        $html = svg($safeName, $safeClass)->toHtml();
    } catch (\Throwable $e) {
        $html = svg('heroicon-o-sparkles', $safeClass)->toHtml();
    }
@endphp

{!! $html !!}
