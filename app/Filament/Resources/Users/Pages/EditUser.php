<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                ...$this->twoFactorComponents(),
                $this->getRelationManagersContentComponent(),
            ]);
    }

    /**
     * Native Filament 2FA management — shown only when you edit your OWN account.
     * 2FA is self-service: the authenticator secret belongs to the account owner,
     * so an admin cannot enrol it on someone else's behalf (they'd only ever
     * manage their own factor here).
     *
     * @return array<Component>
     */
    protected function twoFactorComponents(): array
    {
        if (! Filament::hasMultiFactorAuthentication()
            || $this->getRecord()->getKey() !== Filament::auth()->id()) {
            return [];
        }

        $user = Filament::auth()->user();

        return [
            Section::make('Two-Factor Authentication (2FA)')
                ->description('Aktifkan verifikasi dua langkah dengan aplikasi authenticator (Google Authenticator, Authy, dll.).')
                ->icon('heroicon-o-shield-check')
                ->schema(
                    collect(Filament::getMultiFactorAuthenticationProviders())
                        ->sort(fn (MultiFactorAuthenticationProvider $provider): int => $provider->isEnabled($user) ? 0 : 1)
                        ->map(fn (MultiFactorAuthenticationProvider $provider): Component => Group::make($provider->getManagementSchemaComponents())
                            ->statePath($provider->getId()))
                        ->all(),
                ),
        ];
    }
}
