<?php

namespace App\Filament\Resources\Principles\Pages;

use App\Filament\Resources\Principles\PrincipleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrinciple extends ViewRecord
{
    protected static string $resource = PrincipleResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
