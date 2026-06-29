<div>
    @if ($enabled)
        {{-- Enabled state --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-green-500/30 bg-green-500/10 p-4">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-shield-check" class="h-7 w-7 text-green-500" />
                <div>
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">Two-factor authentication aktif</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Akun Anda diminta kode authenticator setiap login.</p>
                </div>
            </div>
            <x-filament::button color="danger" icon="heroicon-m-lock-open" wire:click="disable" wire:confirm="Nonaktifkan 2FA untuk akun ini?">
                Nonaktifkan
            </x-filament::button>
        </div>
    @else
        {{-- Setup state — QR shown directly --}}
        <div class="space-y-5">
            <div>
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Aktifkan dengan aplikasi authenticator</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pindai QR di bawah dengan Google Authenticator / Authy, lalu masukkan 6 digit kode untuk menyelesaikan.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[auto_1fr]">
                {{-- QR + setup key --}}
                <div class="flex flex-col items-center gap-3">
                    @if ($qrCodeDataUri)
                        <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-gray-950/5">
                            <img src="{{ $qrCodeDataUri }}" alt="QR code 2FA" class="h-44 w-44" />
                        </div>
                    @endif
                    @if ($setupKey)
                        <div class="text-center" x-data="{ copied: false }">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Atau masukkan kunci manual:</p>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ $setupKey }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-sm tracking-wider text-gray-800 transition hover:bg-gray-200 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10">
                                <span>{{ $setupKey }}</span>
                                <x-filament::icon x-show="! copied" icon="heroicon-m-clipboard" class="h-4 w-4" />
                                <x-filament::icon x-show="copied" x-cloak icon="heroicon-m-check" class="h-4 w-4 text-green-500" />
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Recovery codes --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-m-key" class="h-4 w-4 text-amber-500" />
                        <p class="text-sm font-semibold text-gray-950 dark:text-white">Kode pemulihan</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Simpan baik-baik. Dipakai jika perangkat hilang — hanya ditampilkan sekali.</p>
                    <div class="mt-3 grid grid-cols-2 gap-1.5 font-mono text-xs text-gray-700 dark:text-gray-300">
                        @foreach ($recoveryCodes as $recoveryCode)
                            <span class="rounded bg-white px-2 py-1 dark:bg-white/5">{{ $recoveryCode }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Confirm --}}
            <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 dark:border-white/10 sm:flex-row sm:items-end sm:justify-between">
                <div class="w-full sm:max-w-xs">
                    <label for="2fa-code" class="block text-sm font-medium text-gray-950 dark:text-white">Kode 6 digit dari aplikasi</label>
                    <input id="2fa-code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" wire:model="code" wire:keydown.enter="confirm"
                        class="mt-1.5 block w-full rounded-lg border-gray-300 text-center text-lg font-semibold tracking-[0.5em] shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white"
                        placeholder="••••••" />
                    @error('code') <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p> @enderror
                </div>
                <x-filament::button color="success" icon="heroicon-m-check-circle" wire:click="confirm" wire:loading.attr="disabled">
                    Konfirmasi & aktifkan
                </x-filament::button>
            </div>
        </div>
    @endif
</div>
