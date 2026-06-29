{{--
    Backup/recovery codes — the plaintext codes shown once, in a responsive grid
    with a per-code copy button and a download CTA. The heading + description are
    supplied by the surrounding Section (screen 2) or by enabled.blade.php.

    Receives $recoveryCodes (string[]) and, optionally, $completing (bool):
      true  → screen 2 of enrolment: download also finishes setup (→ enabled).
      false → enabled panel after regenerating codes: download only.
--}}
@php($completing = $completing ?? false)
<div class="ct2fa-codeswrap">
    <div class="ct2fa-codes">
        @foreach ($recoveryCodes as $i => $recoveryCode)
            <div class="ct2fa-code" x-data="{ copied: false }">
                <code class="ct2fa-codeval">{{ $recoveryCode }}</code>
                <button
                    type="button"
                    class="ct2fa-copy"
                    x-bind:aria-label="copied ? @js('Kode cadangan '.($i + 1).' tersalin') : @js('Salin kode cadangan '.($i + 1))"
                    x-on:click="navigator.clipboard.writeText(@js($recoveryCode)); copied = true; setTimeout(() => copied = false, 1400)"
                >
                    <x-filament::icon aria-hidden="true" x-show="!copied" icon="heroicon-m-clipboard-document" style="width:1rem;height:1rem" />
                    <x-filament::icon aria-hidden="true" x-show="copied" x-cloak icon="heroicon-m-check" style="width:1rem;height:1rem;color:var(--ct-success)" />
                </button>
            </div>
        @endforeach
    </div>

    @if ($completing)
        <button
            type="button"
            class="ct2fa-btn ct2fa-btn--cta ct2fa-btn--full ct2fa-codesfoot"
            x-data
            x-on:click="await $wire.downloadRecoveryCodes(); $wire.complete()"
        >
            <x-filament::icon icon="heroicon-m-arrow-down-tray" style="width:1.1rem;height:1.1rem" />
            Unduh kode &amp; selesai
        </button>
    @else
        <button
            type="button"
            class="ct2fa-btn ct2fa-btn--ghost ct2fa-btn--sm ct2fa-codesfoot"
            wire:click="downloadRecoveryCodes"
        >
            <x-filament::icon icon="heroicon-m-arrow-down-tray" style="width:1rem;height:1rem" />
            Unduh kode
        </button>
    @endif
</div>
