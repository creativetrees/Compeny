<?php

namespace App\Filament\Resources\ProcessPhases\Pages;

use App\Filament\Resources\ProcessPhases\ProcessPhaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProcessPhase extends EditRecord
{
    protected static string $resource = ProcessPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
