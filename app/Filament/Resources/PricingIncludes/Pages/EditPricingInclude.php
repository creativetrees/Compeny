<?php

namespace App\Filament\Resources\PricingIncludes\Pages;

use App\Filament\Resources\PricingIncludes\PricingIncludeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPricingInclude extends EditRecord
{
    protected static string $resource = PricingIncludeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
