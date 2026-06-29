{{--
    Recovery-codes partial — the plaintext codes, shown once.
    Receives $recoveryCodes (string[]) via viewData. Reused by step 2 of the
    wizard and by the "regenerate codes" flow on the enabled panel.
--}}
<div>
    <div class="ct2fa-codeshead">
        <span class="ct2fa-codestitle">
            <x-filament::icon icon="heroicon-m-key" style="width:1.05rem;height:1.05rem;color:var(--ct-amber)" />
            Kode pemulihan
        </span>
        <span class="ct2fa-chip">
            <x-filament::icon icon="heroicon-m-eye-slash" style="width:.8rem;height:.8rem" />
            Ditampilkan sekali
        </span>
    </div>

    <p class="ct2fa-steptxt" style="margin:0 0 1rem">
        Simpan kode-kode ini di tempat aman. Tiap kode memasukkan Anda satu kali bila kehilangan akses ke ponsel.
    </p>

    <div class="ct2fa-codes">
        @foreach ($recoveryCodes as $i => $recoveryCode)
            <div class="ct2fa-code" x-data="{ copied: false }">
                <span class="ct2fa-codeidx">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <code class="ct2fa-codeval">{{ $recoveryCode }}</code>
                <button
                    type="button"
                    class="ct2fa-copy"
                    aria-label="Salin kode pemulihan {{ $i + 1 }}"
                    x-on:click="navigator.clipboard.writeText(@js($recoveryCode)); copied = true; setTimeout(() => copied = false, 1400)"
                >
                    <x-filament::icon x-show="!copied" icon="heroicon-m-clipboard-document" style="width:1rem;height:1rem" />
                    <x-filament::icon x-show="copied" x-cloak icon="heroicon-m-check" style="width:1rem;height:1rem;color:var(--ct-success)" />
                </button>
            </div>
        @endforeach
    </div>

    <div class="ct2fa-codesfoot">
        <x-filament::button color="gray" size="sm" icon="heroicon-m-arrow-down-tray" wire:click="downloadRecoveryCodes">
            Download codes
        </x-filament::button>
    </div>
</div>
