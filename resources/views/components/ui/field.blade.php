@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'placeholder' => null,
    'options' => [],
])

@php
    $value = old($name);
    $hasError = $errors->has($name);
    $aria = $hasError ? ['aria-invalid' => 'true', 'aria-describedby' => $name.'-error'] : [];
    $borderClass = $hasError ? 'border-2 border-ink' : 'border border-line';
@endphp

<div>
    <label for="{{ $name }}" class="label-mono mb-2 flex items-center gap-1.5">
        {{ $label }}
        @if ($required)<span class="text-faint" aria-hidden="true">*</span><span class="sr-only">(required)</span>@endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="5" @if ($required) required @endif
                  placeholder="{{ $placeholder }}" {{ $attributes->merge($aria) }}
                  class="w-full resize-none {{ $borderClass }} bg-paper px-4 py-3 font-sans text-[0.95rem] text-ink placeholder-faint transition-colors focus:border-ink focus:outline-none">{{ $value }}</textarea>
    @elseif ($type === 'select')
        <div class="relative">
            <select id="{{ $name }}" name="{{ $name }}" @if ($required) required @endif {{ $attributes->merge($aria) }}
                    class="w-full appearance-none {{ $borderClass }} bg-paper px-4 py-3 pr-10 font-sans text-[0.95rem] text-ink transition-colors focus:border-ink focus:outline-none">
                <option value="">{{ $placeholder ?? 'Select' }}</option>
                @foreach ($options as $opt)
                    <option value="{{ $opt }}" @selected($value === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 font-mono text-xs text-muted" aria-hidden="true">▾</span>
        </div>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}"
               @if ($required) required @endif placeholder="{{ $placeholder }}" {{ $attributes->merge($aria) }}
               class="w-full {{ $borderClass }} bg-paper px-4 py-3 font-sans text-[0.95rem] text-ink placeholder-faint transition-colors focus:border-ink focus:outline-none">
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 font-mono text-xs text-ink">↳ {{ $message }}</p>
    @enderror
</div>
