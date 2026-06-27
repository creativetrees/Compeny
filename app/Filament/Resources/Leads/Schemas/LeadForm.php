<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('company'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('budget'),
                TextInput::make('service_interest')
                    ->label('Service interest'),
                Textarea::make('message')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(collect(Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => Str::headline($s)])->all())
                    ->default('new')
                    ->required()
                    ->native(false),
                TextInput::make('source')
                    ->default('website')
                    ->disabled()
                    ->dehydrated(),
                KeyValue::make('meta')
                    ->columnSpanFull(),
            ]);
    }
}
