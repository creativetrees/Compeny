<?php

namespace App\Filament\Resources\NavLinks\Pages;

use App\Filament\Resources\NavLinks\NavLinkResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNavLink extends ViewRecord
{
    protected static string $resource = NavLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
