<?php

namespace App\Filament\Resources\StartSteps\Pages;

use App\Filament\Resources\StartSteps\StartStepResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStartStep extends ViewRecord
{
    protected static string $resource = StartStepResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
