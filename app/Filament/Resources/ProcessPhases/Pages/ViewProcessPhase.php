<?php

namespace App\Filament\Resources\ProcessPhases\Pages;

use App\Filament\Resources\ProcessPhases\ProcessPhaseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProcessPhase extends ViewRecord
{
    protected static string $resource = ProcessPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
