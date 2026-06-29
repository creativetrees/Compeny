{{--
    Enabled-state partial — rendered inside the green "active" Section.
    Receives $remainingCount (int) and $freshRecoveryCodes (string[]) via viewData.
    Used recovery codes are consumed at login, so the remaining count falls over
    time; we never display the stored (hashed) codes — they can't be shown.
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
        <x-filament::button
            color="danger"
            icon="heroicon-m-lock-open"
            wire:click="disable"
            wire:confirm="Nonaktifkan two-factor authentication untuk akun ini?"
        >
            Nonaktifkan
        </x-filament::button>
    </div>

    @if (filled($freshRecoveryCodes))
        <div>
            @include('filament.two-factor.recovery-codes', ['recoveryCodes' => $freshRecoveryCodes])
        </div>
    @endif
</div>
