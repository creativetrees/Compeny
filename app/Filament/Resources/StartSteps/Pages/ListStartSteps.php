<?php

namespace App\Filament\Resources\StartSteps\Pages;

use App\Filament\Resources\StartSteps\StartStepResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStartSteps extends ListRecords
{
    protected static string $resource = StartStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
