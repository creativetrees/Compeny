{{--
    Step 1 partial — scan the QR or type the setup key by hand.
    Receives $qrCodeDataUri (string|null) and $setupKey (string|null) via viewData.
--}}
<div class="ct2fa-scan">
    <div class="ct2fa-tilewrap">
        @if ($qrCodeDataUri)
            <div class="ct2fa-tile">
                <img src="{{ $qrCodeDataUri }}" alt="Kode QR untuk menambahkan akun ke aplikasi authenticator" class="ct2fa-qr" />
            </div>
        @endif
        <span class="ct2fa-tilehint">
            <x-filament::icon icon="heroicon-m-qr-code" style="width:.95rem;height:.95rem" />
            Pindai dengan aplikasi authenticator
        </span>
    </div>

    <div class="ct2fa-manual">
        <div class="ct2fa-step">
            <span class="ct2fa-num">1</span>
            <span class="ct2fa-steptxt">Buka <b>Google Authenticator</b>, <b>Authy</b>, atau <b>1Password</b>.</span>
        </div>
        <div class="ct2fa-step">
            <span class="ct2fa-num">2</span>
            <span class="ct2fa-steptxt">Pindai kode di samping — atau masukkan kunci ini secara manual.</span>
        </div>

        @if ($setupKey)
            <p class="ct2fa-manlabel">Kunci penyiapan manual</p>
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
        @endif
    </div>
</div>
