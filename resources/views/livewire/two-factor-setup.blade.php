<div class="ctg2fa-root">
    {{-- Scoped styles. The panel CSS is purged, so Tailwind utilities aren't compiled here —
         this component ships its own semantic classes (ctg2fa-* prefix). Colours are built from
         color-mix(currentColor …) so they adapt to both the light and dark zinc themes.
         wire:ignore stops Livewire from re-morphing the <style> on every update. --}}
    <style wire:ignore>
        .ctg2fa-root {
            --g2-success: #22c55e;
            --g2-amber: #f59e0b;
            --g2-border: color-mix(in srgb, currentColor 12%, transparent);
            --g2-border-strong: color-mix(in srgb, currentColor 22%, transparent);
            --g2-surface: color-mix(in srgb, currentColor 4%, transparent);
            --g2-surface-2: color-mix(in srgb, currentColor 7%, transparent);
            --g2-muted: color-mix(in srgb, currentColor 56%, transparent);
            --g2-faint: color-mix(in srgb, currentColor 32%, transparent);
            --g2-mono: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace;
        }
        .ctg2fa-root [x-cloak] { display: none !important; }

        /* Intro header */
        .ctg2fa-head { display: flex; align-items: flex-start; gap: .9rem; margin-bottom: 1.85rem; }
        .ctg2fa-headicon { flex: none; width: 2.6rem; height: 2.6rem; border-radius: .8rem; display: grid; place-items: center;
            background: color-mix(in srgb, var(--g2-success) 12%, transparent);
            border: 1px solid color-mix(in srgb, var(--g2-success) 30%, transparent); }
        .ctg2fa-eyebrow { text-transform: uppercase; letter-spacing: .16em; font-size: .67rem; font-weight: 700; margin: 0 0 .25rem;
            color: color-mix(in srgb, var(--g2-success) 82%, currentColor); }
        .ctg2fa-title { font-size: 1.05rem; font-weight: 600; margin: 0; line-height: 1.25; }
        .ctg2fa-sub { font-size: .85rem; color: var(--g2-muted); margin: .3rem 0 0; line-height: 1.5; }

        /* Stepper (real sequence: scan → save → verify) */
        .ctg2fa-steps { list-style: none; margin: 0; padding: 0; }
        .ctg2fa-step { display: grid; grid-template-columns: 2.5rem minmax(0, 1fr); column-gap: 1.1rem; padding-bottom: 1.85rem; }
        .ctg2fa-step:last-child { padding-bottom: 0; }
        .ctg2fa-rail { display: flex; flex-direction: column; align-items: center; }
        .ctg2fa-node { flex: none; width: 2.5rem; height: 2.5rem; border-radius: 50%; display: grid; place-items: center;
            font-weight: 700; font-size: .95rem; font-variant-numeric: tabular-nums;
            color: color-mix(in srgb, currentColor 78%, transparent);
            background: var(--g2-surface-2); border: 1px solid var(--g2-border-strong);
            box-shadow: 0 1px 2px color-mix(in srgb, currentColor 9%, transparent); }
        .ctg2fa-node--goal { color: var(--g2-success);
            background: color-mix(in srgb, var(--g2-success) 13%, transparent);
            border-color: color-mix(in srgb, var(--g2-success) 40%, transparent); }
        .ctg2fa-line { flex: 1 1 auto; width: 2px; min-height: 1rem; margin: .4rem 0; border-radius: 2px;
            background: linear-gradient(to bottom, var(--g2-border-strong), color-mix(in srgb, currentColor 4%, transparent)); }

        .ctg2fa-stepbody { min-width: 0; padding-top: .12rem; }
        .ctg2fa-stepeyebrow { text-transform: uppercase; letter-spacing: .14em; font-size: .65rem; font-weight: 700;
            color: var(--g2-muted); margin: 0 0 .2rem; }
        .ctg2fa-steptitle { font-size: 1rem; font-weight: 600; margin: 0; line-height: 1.25; }
        .ctg2fa-stepsub { font-size: .84rem; color: var(--g2-muted); margin: .3rem 0 0; line-height: 1.5; }

        .ctg2fa-panel { margin-top: .95rem; border: 1px solid var(--g2-border); background: var(--g2-surface);
            border-radius: .95rem; padding: 1.15rem; }

        /* Step 1 — scan */
        .ctg2fa-scan { display: grid; gap: 1.3rem; }
        @media (min-width: 1024px) { .ctg2fa-scan { grid-template-columns: auto minmax(0, 1fr); align-items: center; } }
        .ctg2fa-qrwrap { display: flex; flex-direction: column; align-items: center; gap: .85rem; }
        .ctg2fa-qrbox { background: #fff; padding: .6rem; border-radius: 1rem; max-width: 100%;
            box-shadow: 0 10px 28px -12px rgba(0,0,0,.45), 0 0 0 1px color-mix(in srgb, currentColor 8%, transparent); }
        .ctg2fa-qr { display: block; width: 224px; height: 224px; max-width: 100%; border-radius: .35rem; }

        .ctg2fa-manual { min-width: 0; }
        .ctg2fa-manlabel { font-size: .82rem; color: var(--g2-muted); margin: 0 0 .55rem; }
        .ctg2fa-keyrow { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; }
        .ctg2fa-keypill { flex: 1 1 12rem; min-width: 0; font-family: var(--g2-mono); font-size: .85rem; letter-spacing: .07em;
            padding: .58rem .75rem; border-radius: .6rem; background: var(--g2-surface-2);
            border: 1px solid var(--g2-border); word-break: break-all; }

        /* Step 2 — recovery codes */
        .ctg2fa-chip { display: inline-flex; align-items: center; gap: .25rem; vertical-align: middle; margin-left: .35rem;
            padding: .12rem .5rem; border-radius: 999px; font-size: .67rem; font-weight: 600;
            color: color-mix(in srgb, var(--g2-amber) 92%, currentColor);
            background: color-mix(in srgb, var(--g2-amber) 14%, transparent);
            border: 1px solid color-mix(in srgb, var(--g2-amber) 32%, transparent); }
        .ctg2fa-codes { display: grid; grid-template-columns: 1fr; gap: .5rem; }
        @media (min-width: 560px) { .ctg2fa-codes { grid-template-columns: 1fr 1fr; } }
        .ctg2fa-codepill { display: flex; align-items: center; justify-content: space-between; gap: .5rem;
            padding: .35rem .35rem .35rem .75rem; border-radius: .55rem;
            border: 1px solid var(--g2-border); background: var(--g2-surface-2); }
        .ctg2fa-codeval { font-family: var(--g2-mono); font-size: .82rem; letter-spacing: .05em; }
        .ctg2fa-iconbtn { flex: none; display: grid; place-items: center; width: 1.9rem; height: 1.9rem; border: 0;
            border-radius: .45rem; cursor: pointer; color: var(--g2-muted); background: transparent;
            transition: background .15s, color .15s; }
        .ctg2fa-iconbtn:hover { background: color-mix(in srgb, currentColor 10%, transparent); color: inherit; }
        .ctg2fa-iconbtn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, currentColor 18%, transparent); }
        .ctg2fa-codesfoot { margin-top: 1rem; display: flex; justify-content: flex-end; }

        /* Step 3 — verify */
        .ctg2fa-verify { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem; }
        .ctg2fa-otplabel { display: block; font-size: .8rem; font-weight: 600; margin-bottom: .4rem; color: var(--g2-muted); }
        .ctg2fa-otp { width: min(15rem, 100%); text-align: center; font-family: var(--g2-mono); font-size: 1.5rem; font-weight: 700;
            letter-spacing: .45em; text-indent: .45em; font-variant-numeric: tabular-nums;
            padding: .65rem .8rem; border-radius: .7rem; color: inherit;
            border: 1px solid var(--g2-border-strong); background: var(--g2-surface);
            transition: border-color .15s, box-shadow .15s; }
        .ctg2fa-otp::placeholder { color: var(--g2-faint); }
        .ctg2fa-otp:focus { outline: none; border-color: color-mix(in srgb, var(--g2-success) 55%, transparent);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--g2-success) 16%, transparent); }
        .ctg2fa-err { display: flex; align-items: center; gap: .3rem; color: #ef4444; font-size: .8rem; margin: .5rem 0 0; }

        /* Enabled state */
        .ctg2fa-active { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem;
            border-radius: 1rem; padding: 1.3rem 1.45rem;
            border: 1px solid color-mix(in srgb, var(--g2-success) 38%, transparent);
            background: color-mix(in srgb, var(--g2-success) 10%, transparent); }
        .ctg2fa-activeinfo { display: flex; align-items: center; gap: .95rem; min-width: 0; }
        .ctg2fa-activeicon { flex: none; width: 3rem; height: 3rem; border-radius: .85rem; display: grid; place-items: center;
            background: color-mix(in srgb, var(--g2-success) 16%, transparent);
            border: 1px solid color-mix(in srgb, var(--g2-success) 32%, transparent); }
        .ctg2fa-activebtns { display: flex; flex-wrap: wrap; gap: .6rem; }

        @media (prefers-reduced-motion: reduce) {
            .ctg2fa-otp, .ctg2fa-iconbtn { transition: none; }
        }
    </style>

    @if ($enabled)
        <div class="ctg2fa-active">
            <div class="ctg2fa-activeinfo">
                <span class="ctg2fa-activeicon">
                    <x-filament::icon icon="heroicon-o-shield-check" style="width:1.6rem;height:1.6rem;color:var(--g2-success)" />
                </span>
                <div>
                    <p class="ctg2fa-eyebrow">Aktif</p>
                    <h3 class="ctg2fa-title">Two-factor authentication aktif</h3>
                    <p class="ctg2fa-sub">Setiap login meminta kode dari aplikasi authenticator Anda.</p>
                </div>
            </div>
            <div class="ctg2fa-activebtns">
                @if (filled($recoveryCodes))
                    <x-filament::button color="gray" icon="heroicon-m-arrow-down-tray" wire:click="downloadRecoveryCodes">
                        Download codes
                    </x-filament::button>
                @endif
                <x-filament::button color="danger" icon="heroicon-m-lock-open" wire:click="disable"
                    wire:confirm="Nonaktifkan 2FA untuk akun ini?">
                    Nonaktifkan
                </x-filament::button>
            </div>
        </div>
    @else
        <div class="ctg2fa-head">
            <span class="ctg2fa-headicon">
                <x-filament::icon icon="heroicon-o-shield-check" style="width:1.4rem;height:1.4rem;color:var(--g2-success)" />
            </span>
            <div>
                <p class="ctg2fa-eyebrow">Verifikasi dua langkah</p>
                <h3 class="ctg2fa-title">Aktifkan aplikasi authenticator</h3>
                <p class="ctg2fa-sub">Tambahkan lapisan kedua — setiap login meminta kode 6 digit dari aplikasi di ponsel Anda. Selesaikan tiga langkah di bawah.</p>
            </div>
        </div>

        <ol class="ctg2fa-steps">
            {{-- Step 1: scan --}}
            <li class="ctg2fa-step">
                <div class="ctg2fa-rail">
                    <span class="ctg2fa-node">1</span>
                    <span class="ctg2fa-line"></span>
                </div>
                <div class="ctg2fa-stepbody">
                    <p class="ctg2fa-stepeyebrow">Aplikasi authenticator</p>
                    <h4 class="ctg2fa-steptitle">Pindai kode QR</h4>
                    <p class="ctg2fa-stepsub">Buka Google Authenticator, Authy, atau 1Password, lalu pindai kode ini.</p>

                    <div class="ctg2fa-panel ctg2fa-scan">
                        <div class="ctg2fa-qrwrap">
                            @if ($qrCodeDataUri)
                                <div class="ctg2fa-qrbox">
                                    <img src="{{ $qrCodeDataUri }}" alt="Kode QR untuk menambahkan akun 2FA" class="ctg2fa-qr" />
                                </div>
                            @endif
                            <x-filament::button color="gray" size="sm" icon="heroicon-m-arrow-path"
                                wire:click="regenerate" wire:target="regenerate" wire:loading.attr="disabled"
                                wire:confirm="Buat QR & kode pemulihan baru? Kode lama akan dibatalkan.">
                                Buat ulang QR
                            </x-filament::button>
                        </div>

                        <div class="ctg2fa-manual">
                            <p class="ctg2fa-manlabel">Tidak bisa scan? Masukkan kunci manual:</p>
                            @if ($setupKey)
                                <div class="ctg2fa-keyrow" x-data="{ copied: false }">
                                    <code class="ctg2fa-keypill">{{ $setupKey }}</code>
                                    <x-filament::button color="gray" size="sm" icon="heroicon-m-clipboard"
                                        x-on:click="navigator.clipboard.writeText(@js($setupKey)); copied = true; setTimeout(() => copied = false, 1600)">
                                        <span x-show="!copied">Salin kode</span>
                                        <span x-show="copied" x-cloak>Tersalin</span>
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>

            {{-- Step 2: recovery codes --}}
            <li class="ctg2fa-step">
                <div class="ctg2fa-rail">
                    <span class="ctg2fa-node">2</span>
                    <span class="ctg2fa-line"></span>
                </div>
                <div class="ctg2fa-stepbody">
                    <p class="ctg2fa-stepeyebrow">Cadangan akses</p>
                    <h4 class="ctg2fa-steptitle">Simpan kode pemulihan</h4>
                    <p class="ctg2fa-stepsub">
                        Dipakai untuk masuk jika ponsel hilang.
                        <span class="ctg2fa-chip">
                            <x-filament::icon icon="heroicon-m-exclamation-triangle" style="width:.85rem;height:.85rem" />
                            Ditampilkan sekali
                        </span>
                    </p>

                    <div class="ctg2fa-panel">
                        <div class="ctg2fa-codes">
                            @foreach ($recoveryCodes as $recoveryCode)
                                <div class="ctg2fa-codepill" x-data="{ copied: false }">
                                    <code class="ctg2fa-codeval">{{ $recoveryCode }}</code>
                                    <button type="button" class="ctg2fa-iconbtn" aria-label="Salin kode pemulihan"
                                        x-on:click="navigator.clipboard.writeText(@js($recoveryCode)); copied = true; setTimeout(() => copied = false, 1400)">
                                        <x-filament::icon x-show="!copied" icon="heroicon-m-clipboard" style="width:.95rem;height:.95rem" />
                                        <x-filament::icon x-show="copied" x-cloak icon="heroicon-m-check" style="width:.95rem;height:.95rem;color:var(--g2-success)" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="ctg2fa-codesfoot">
                            <x-filament::button color="gray" size="sm" icon="heroicon-m-arrow-down-tray" wire:click="downloadRecoveryCodes">
                                Download codes
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </li>

            {{-- Step 3: verify --}}
            <li class="ctg2fa-step">
                <div class="ctg2fa-rail">
                    <span class="ctg2fa-node ctg2fa-node--goal">3</span>
                </div>
                <div class="ctg2fa-stepbody">
                    <p class="ctg2fa-stepeyebrow">Konfirmasi</p>
                    <h4 class="ctg2fa-steptitle">Masukkan kode verifikasi</h4>
                    <p class="ctg2fa-stepsub">Ketik 6 digit yang sedang tampil di aplikasi untuk menyalakan 2FA.</p>

                    <div class="ctg2fa-panel ctg2fa-verify">
                        <div>
                            <label for="ctg2fa-code" class="ctg2fa-otplabel">Kode 6 digit</label>
                            <input id="ctg2fa-code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                                wire:model="code" wire:keydown.enter="confirm" class="ctg2fa-otp" placeholder="000000" />
                            @error('code')
                                <p class="ctg2fa-err">
                                    <x-filament::icon icon="heroicon-m-exclamation-circle" style="width:.95rem;height:.95rem" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <x-filament::button color="success" icon="heroicon-m-check-circle" wire:click="confirm"
                            wire:target="confirm" wire:loading.attr="disabled">
                            Konfirmasi &amp; aktifkan
                        </x-filament::button>
                    </div>
                </div>
            </li>
        </ol>
    @endif
</div>
