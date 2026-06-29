{{-- Step 1 partial — the QR tile (left column). Receives $qrCodeDataUri (string|null). --}}
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
