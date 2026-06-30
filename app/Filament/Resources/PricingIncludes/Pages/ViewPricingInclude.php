<?php

namespace App\Filament\Resources\PricingIncludes\Pages;

use App\Filament\Resources\PricingIncludes\PricingIncludeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPricingInclude extends ViewRecord
{
    protected static string $resource = PricingIncludeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
