<?php

namespace App\Filament\Resources\SiteContents;

use App\Filament\Resources\SiteContents\Pages\CreateSiteContent;
use App\Filament\Resources\SiteContents\Pages\EditSiteContent;
use App\Filament\Resources\SiteContents\Pages\ListSiteContents;
use App\Filament\Resources\SiteContents\Schemas\SiteContentForm;
use App\Filament\Resources\SiteContents\Tables\SiteContentsTable;
use App\Models\SiteContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiteContentResource extends Resource
{
    protected static ?string $model = SiteContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Site Content';

    protected static ?string $modelLabel = 'Site Content';

    public static function form(Schema $schema): Schema
    {
        return SiteContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteContentsTable::configure($table);
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
            'index' => ListSiteContents::route('/'),
            'create' => CreateSiteContent::route('/create'),
            'edit' => EditSiteContent::route('/{record}/edit'),
        ];
    }
}
