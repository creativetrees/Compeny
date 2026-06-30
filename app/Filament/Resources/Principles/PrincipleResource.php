<?php

namespace App\Filament\Resources\Principles;

use App\Filament\Resources\Principles\Pages\CreatePrinciple;
use App\Filament\Resources\Principles\Pages\EditPrinciple;
use App\Filament\Resources\Principles\Pages\ListPrinciples;
use App\Filament\Resources\Principles\Pages\ViewPrinciple;
use App\Filament\Resources\Principles\Schemas\PrincipleForm;
use App\Filament\Resources\Principles\Schemas\PrincipleInfolist;
use App\Filament\Resources\Principles\Tables\PrinciplesTable;
use App\Models\Principle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrincipleResource extends Resource
{
    protected static ?string $model = Principle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 13;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PrincipleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrincipleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrinciplesTable::configure($table);
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
            'index' => ListPrinciples::route('/'),
            'create' => CreatePrinciple::route('/create'),
            'view' => ViewPrinciple::route('/{record}'),
            'edit' => EditPrinciple::route('/{record}/edit'),
        ];
    }
}
