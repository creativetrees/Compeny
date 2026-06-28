<?php

namespace App\Filament\Resources\NavLinks;

use App\Filament\Resources\NavLinks\Pages\CreateNavLink;
use App\Filament\Resources\NavLinks\Pages\EditNavLink;
use App\Filament\Resources\NavLinks\Pages\ListNavLinks;
use App\Filament\Resources\NavLinks\Schemas\NavLinkForm;
use App\Filament\Resources\NavLinks\Tables\NavLinksTable;
use App\Models\NavLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NavLinkResource extends Resource
{
    protected static ?string $model = NavLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return NavLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NavLinksTable::configure($table);
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
            'index' => ListNavLinks::route('/'),
            'create' => CreateNavLink::route('/create'),
            'edit' => EditNavLink::route('/{record}/edit'),
        ];
    }
}
