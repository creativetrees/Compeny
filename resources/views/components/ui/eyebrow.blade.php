@props(['plain' => false])

<span {{ $attributes->merge(['class' => 'eyebrow' . ($plain ? ' eyebrow--plain' : '')]) }}>
    {{ $slot }}
</span>
