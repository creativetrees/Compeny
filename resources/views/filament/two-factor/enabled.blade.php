{{--
    Enabled-state partial — rendered inside the green "active" Section.
    Receives $remainingCount (int) and $freshRecoveryCodes (string[]) via viewData.
    Used recovery codes are consumed at login, so the remaining count falls over
    time; we never display the stored (hashed) codes — they can't be shown.
    Disabling requires the account password (re-auth) via wire:model="disablePassword".
--}}
<div class="ct2fa-enabled">
    <div class="ct2fa-meter">
        <span class="ct2fa-metericon">
            <x-filament::icon icon="heroicon-m-key" style="width:1.3rem;height:1.3rem" />
        </span>
        <div style="flex:1 1 auto;min-width:0">
            <div class="ct2fa-metercount">{{ $remainingCount }}</div>
            <p class="ct2fa-meterlabel">kode pemulihan tersisa</p>
        </div>
    </div>

    @if (filled($freshRecoveryCodes))
        <div>
            @include('filament.two-factor.recovery-codes', ['recoveryCodes' => $freshRecoveryCodes])
        </div>
    @endif

    {{-- Danger zone — disabling 2FA requires the account password (re-auth). --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:1rem;padding:1.05rem 1.15rem;border-radius:.9rem;border:1px solid color-mix(in srgb, #dc2626 30%, transparent);background:color-mix(in srgb, #dc2626 7%, transparent)">
        <div style="min-width:0">
            <p style="font-weight:600;font-size:.85rem">Nonaktifkan 2FA</p>
            <p class="ct2fa-meterlabel" style="margin-top:.15rem">Masukkan password akun Anda untuk konfirmasi.</p>
            <input
                type="password"
                wire:model="disablePassword"
                wire:keydown.enter="disable"
                placeholder="Password Anda"
                autocomplete="current-password"
                style="margin-top:.55rem;width:min(17rem,100%);padding:.52rem .72rem;border-radius:.55rem;font-size:.9rem;color:inherit;border:1px solid color-mix(in srgb,currentColor 20%,transparent);background:color-mix(in srgb,currentColor 4%,transparent)"
            />
            @error('disablePassword')
                <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
            @enderror
        </div>
        <x-filament::button
            color="danger"
            icon="heroicon-m-lock-open"
            wire:click="disable"
            wire:target="disable"
            wire:loading.attr="disabled"
        >
            Nonaktifkan
        </x-filament::button>
    </div>
</div>
