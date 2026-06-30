<?php

namespace App\Filament\Resources\PricingIncludes;

use App\Filament\Resources\PricingIncludes\Pages\CreatePricingInclude;
use App\Filament\Resources\PricingIncludes\Pages\EditPricingInclude;
use App\Filament\Resources\PricingIncludes\Pages\ListPricingIncludes;
use App\Filament\Resources\PricingIncludes\Schemas\PricingIncludeForm;
use App\Filament\Resources\PricingIncludes\Tables\PricingIncludesTable;
use App\Models\PricingInclude;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PricingIncludeResource extends Resource
{
    protected static ?string $model = PricingInclude::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationParentItem = 'Pricing';

    public static function form(Schema $schema): Schema
    {
        return PricingIncludeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricingIncludesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPricingIncludes::route('/'),
            'create' => CreatePricingInclude::route('/create'),
            'edit' => EditPricingInclude::route('/{record}/edit'),
        ];
    }
}
