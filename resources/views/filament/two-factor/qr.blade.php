{{--
    Screen 1, left column — "Pindai kode QR": the QR tile with the manual setup
    key + copy button. On desktop they sit side-by-side (QR left, key right); on
    mobile they stack with the QR centred. Receives $qrCodeDataUri (string|null)
    and $setupKey (string|null) via viewData.
--}}
<div class="ct2fa-block">
    <div class="ct2fa-head">
        <span class="ct2fa-headicon">
            <x-filament::icon aria-hidden="true" icon="heroicon-o-qr-code" style="width:1.35rem;height:1.35rem" />
        </span>
        <h3 class="ct2fa-title">Pindai kode QR</h3>
    </div>
    <p class="ct2fa-sub">
        Buka aplikasi authenticator (Google Authenticator, Authy, 1Password) lalu pindai kode di samping.
    </p>

    <div class="ct2fa-card">
        <div class="ct2fa-cardgrid">
            @if ($qrCodeDataUri)
                <div class="ct2fa-qrtile">
                    <img
                        src="{{ $qrCodeDataUri }}"
                        alt="Kode QR untuk aplikasi authenticator — jika tidak bisa dipindai, gunakan kunci manual di samping"
                        class="ct2fa-qr"
                    />
                </div>
            @endif

            <div class="ct2fa-manual" x-data="{ copied: false }">
                <span class="ct2fa-manlabel">Tidak bisa memindai? Masukkan kunci ini secara manual:</span>
                <code class="ct2fa-key">{{ $setupKey }}</code>
                <button
                    type="button"
                    class="ct2fa-btn ct2fa-btn--ghost ct2fa-btn--sm ct2fa-copycode"
                    x-on:click="navigator.clipboard.writeText(@js($setupKey)); copied = true; setTimeout(() => copied = false, 1600)"
                >
                    <x-filament::icon aria-hidden="true" x-show="!copied" icon="heroicon-m-clipboard-document" style="width:1rem;height:1rem" />
                    <x-filament::icon aria-hidden="true" x-show="copied" x-cloak icon="heroicon-m-check" style="width:1rem;height:1rem" />
                    <span x-show="!copied">Salin kode</span>
                    <span x-show="copied" x-cloak>Tersalin</span>
                </button>
                <span role="status" aria-live="polite" class="ct2fa-sr" x-text="copied ? 'Kunci penyiapan tersalin' : ''"></span>
            </div>
        </div>
    </div>
</div>
