{{--
    Screen 1 footer — the Batal / Verifikasi buttons, spanning the full Section
    width below the two columns. Plain wire:click calls (not Filament Actions) so
    the verifyAndEnable() validation error renders inline on the OTP field. No viewData.
--}}
<div class="ct2fa-actionbar">
    <button
        type="button"
        class="ct2fa-btn ct2fa-btn--ghost"
        wire:click="cancelSetup"
        wire:loading.attr="disabled"
    >
        Batal
    </button>
    <button
        type="button"
        class="ct2fa-btn ct2fa-btn--cta"
        wire:click="verifyAndEnable"
        wire:target="verifyAndEnable"
        wire:loading.attr="disabled"
    >
        <x-filament::icon
            aria-hidden="true"
            icon="heroicon-m-arrow-path"
            class="ct2fa-spin"
            style="width:1.05rem;height:1.05rem"
            wire:loading
            wire:target="verifyAndEnable"
        />
        <span wire:loading.remove wire:target="verifyAndEnable">Verifikasi</span>
        <span wire:loading wire:target="verifyAndEnable">Memverifikasi…</span>
    </button>
</div>
