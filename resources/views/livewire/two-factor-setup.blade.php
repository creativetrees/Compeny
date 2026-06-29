<div>
    {{-- Scoped styles (Tailwind utilities aren't compiled into the purged panel CSS, so
         this component ships its own). wire:ignore keeps Livewire from re-morphing it. --}}
    <style wire:ignore>
        .ctg2fa-head { margin-bottom: 1.25rem; }
        .ctg2fa-title { font-size: 1rem; font-weight: 600; }
        .ctg2fa-sub { font-size: .875rem; margin-top: .25rem; color: color-mix(in srgb, currentColor 55%, transparent); }
        .ctg2fa-grid { display: grid; gap: 1.5rem; align-items: start; }
        @media (min-width: 1024px) { .ctg2fa-grid { grid-template-columns: auto minmax(0, 1fr); } }
        .ctg2fa-qrbox { display: inline-block; background: #fff; padding: .75rem; border-radius: .875rem; box-shadow: 0 1px 3px rgba(0,0,0,.12); }
        .ctg2fa-qr { display: block; width: 168px; height: 168px; }
        .ctg2fa-keylabel { font-size: .75rem; margin-top: .75rem; color: color-mix(in srgb, currentColor 55%, transparent); }
        .ctg2fa-key { display: inline-flex; align-items: center; gap: .5rem; margin-top: .375rem; padding: .375rem .625rem; border-radius: .5rem; cursor: pointer;
            font-family: ui-monospace, monospace; letter-spacing: .08em; font-size: .8rem;
            background: color-mix(in srgb, currentColor 8%, transparent); border: 0; color: inherit; transition: background .15s; }
        .ctg2fa-key:hover { background: color-mix(in srgb, currentColor 14%, transparent); }
        .ctg2fa-card { border: 1px solid color-mix(in srgb, currentColor 12%, transparent); background: color-mix(in srgb, currentColor 4%, transparent); border-radius: .875rem; padding: 1rem; }
        .ctg2fa-cardtitle { display: flex; align-items: center; gap: .375rem; font-weight: 600; font-size: .875rem; }
        .ctg2fa-codes { display: grid; grid-template-columns: 1fr 1fr; gap: .375rem; margin-top: .75rem; font-family: ui-monospace, monospace; font-size: .72rem; }
        .ctg2fa-code { background: color-mix(in srgb, currentColor 7%, transparent); border-radius: .375rem; padding: .25rem .5rem; text-align: center; }
        .ctg2fa-actions { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid color-mix(in srgb, currentColor 10%, transparent); }
        .ctg2fa-otplabel { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .375rem; }
        .ctg2fa-otp { width: 11rem; text-align: center; letter-spacing: .5em; font-size: 1.25rem; font-weight: 700; padding: .55rem .75rem; border-radius: .625rem;
            border: 1px solid color-mix(in srgb, currentColor 18%, transparent); background: color-mix(in srgb, currentColor 4%, transparent); color: inherit; }
        .ctg2fa-otp:focus { outline: none; border-color: color-mix(in srgb, currentColor 45%, transparent); box-shadow: 0 0 0 3px color-mix(in srgb, currentColor 10%, transparent); }
        .ctg2fa-err { color: #ef4444; font-size: .8rem; margin-top: .375rem; }
        .ctg2fa-enabled { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; border-radius: .875rem; padding: 1rem 1.25rem;
            border: 1px solid color-mix(in srgb, #22c55e 40%, transparent); background: color-mix(in srgb, #22c55e 12%, transparent); }
    </style>

    @if ($enabled)
        <div class="ctg2fa-enabled">
            <div style="display:flex;align-items:center;gap:.75rem">
                <x-filament::icon icon="heroicon-o-shield-check" style="width:1.75rem;height:1.75rem;color:#22c55e" />
                <div>
                    <p style="font-weight:600">Two-factor authentication aktif</p>
                    <p class="ctg2fa-sub" style="margin-top:0">Akun diminta kode authenticator setiap login.</p>
                </div>
            </div>
            <x-filament::button color="danger" icon="heroicon-m-lock-open" wire:click="disable" wire:confirm="Nonaktifkan 2FA untuk akun ini?">
                Nonaktifkan
            </x-filament::button>
        </div>
    @else
        <div class="ctg2fa-head">
            <h3 class="ctg2fa-title">Aktifkan dengan aplikasi authenticator</h3>
            <p class="ctg2fa-sub">Pindai QR berikut dengan Google Authenticator / Authy, lalu masukkan 6 digit kode untuk menyelesaikan.</p>
        </div>

        <div class="ctg2fa-grid">
            <div>
                @if ($qrCodeDataUri)
                    <div class="ctg2fa-qrbox"><img src="{{ $qrCodeDataUri }}" alt="QR code 2FA" class="ctg2fa-qr" /></div>
                @endif
                @if ($setupKey)
                    <div>
                        <p class="ctg2fa-keylabel">Atau masukkan kunci manual:</p>
                        <button type="button" class="ctg2fa-key" x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText('{{ $setupKey }}'); copied = true; setTimeout(() => copied = false, 1500)">
                            <span>{{ $setupKey }}</span>
                            <x-filament::icon x-show="!copied" icon="heroicon-m-clipboard" style="width:1rem;height:1rem" />
                            <x-filament::icon x-show="copied" x-cloak icon="heroicon-m-check" style="width:1rem;height:1rem;color:#22c55e" />
                        </button>
                    </div>
                @endif
            </div>

            <div class="ctg2fa-card">
                <p class="ctg2fa-cardtitle">
                    <x-filament::icon icon="heroicon-m-key" style="width:1rem;height:1rem;color:#f59e0b" /> Kode pemulihan
                </p>
                <p class="ctg2fa-sub" style="margin-top:.25rem">Simpan baik-baik — dipakai jika perangkat hilang, hanya ditampilkan sekali.</p>
                <div class="ctg2fa-codes">
                    @foreach ($recoveryCodes as $recoveryCode)
                        <span class="ctg2fa-code">{{ $recoveryCode }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ctg2fa-actions">
            <div>
                <label for="2fa-code" class="ctg2fa-otplabel">Kode 6 digit dari aplikasi</label>
                <input id="2fa-code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                    wire:model="code" wire:keydown.enter="confirm" class="ctg2fa-otp" placeholder="••••••" />
                @error('code') <p class="ctg2fa-err">{{ $message }}</p> @enderror
            </div>
            <x-filament::button color="success" icon="heroicon-m-check-circle" wire:click="confirm" wire:loading.attr="disabled">
                Konfirmasi &amp; aktifkan
            </x-filament::button>
        </div>
    @endif
</div>
