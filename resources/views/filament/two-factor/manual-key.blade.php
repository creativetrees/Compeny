{{-- Step 1 partial — the manual setup key + copy button (right column, above the OTP).
     Receives $setupKey (string|null) via viewData. --}}
@if ($setupKey)
    <div style="margin-bottom:1.1rem">
        <p class="ct2fa-manlabel" style="margin-top:0">Kunci penyiapan manual</p>
        <div class="ct2fa-keyrow" x-data="{ copied: false }">
            <code class="ct2fa-key">{{ $setupKey }}</code>
            <x-filament::button
                color="gray"
                size="sm"
                icon="heroicon-m-clipboard-document"
                x-on:click="navigator.clipboard.writeText(@js($setupKey)); copied = true; setTimeout(() => copied = false, 1600)"
            >
                <span x-show="!copied">Salin</span>
                <span x-show="copied" x-cloak>Tersalin</span>
            </x-filament::button>
        </div>
    </div>
@endif
