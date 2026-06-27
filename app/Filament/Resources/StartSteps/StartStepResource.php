<?php

namespace App\Filament\Resources\StartSteps;

use App\Filament\Resources\StartSteps\Pages\CreateStartStep;
use App\Filament\Resources\StartSteps\Pages\EditStartStep;
use App\Filament\Resources\StartSteps\Pages\ListStartSteps;
use App\Filament\Resources\StartSteps\Schemas\StartStepForm;
use App\Filament\Resources\StartSteps\Tables\StartStepsTable;
use App\Models\StartStep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StartStepResource extends Resource
{
    protected static ?string $model = StartStep::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;
    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return StartStepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StartStepsTable::configure($table);
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
            'index' => ListStartSteps::route('/'),
            'create' => CreateStartStep::route('/create'),
            'edit' => EditStartStep::route('/{record}/edit'),
        ];
    }
}
