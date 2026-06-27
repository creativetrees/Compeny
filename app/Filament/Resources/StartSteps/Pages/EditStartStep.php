<?php

namespace App\Filament\Resources\StartSteps\Pages;

use App\Filament\Resources\StartSteps\StartStepResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStartStep extends EditRecord
{
    protected static string $resource = StartStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
