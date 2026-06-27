<?php

namespace App\Filament\Resources\PricingIncludes\Pages;

use App\Filament\Resources\PricingIncludes\PricingIncludeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPricingIncludes extends ListRecords
{
    protected static string $resource = PricingIncludeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
