<?php

namespace App\Filament\Resources\ProcessPhases\Pages;

use App\Filament\Resources\ProcessPhases\ProcessPhaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcessPhases extends ListRecords
{
    protected static string $resource = ProcessPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
