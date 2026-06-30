<?php

namespace App\Filament\Resources\PricingTiers\Pages;

use App\Filament\Resources\PricingTiers\PricingTierResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPricingTier extends ViewRecord
{
    protected static string $resource = PricingTierResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
