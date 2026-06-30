<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->prefixIcon('heroicon-m-user'),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->prefixIcon('heroicon-m-envelope'),
                        TextInput::make('company')
                            ->prefixIcon('heroicon-m-building-office-2'),
                        TextInput::make('phone')
                            ->tel()
                            ->prefixIcon('heroicon-m-phone'),
                    ]),

                Section::make('Detail')
                    ->icon('heroicon-o-inbox-stack')
                    ->columns(2)
                    ->schema([
                        TextInput::make('budget')
                            ->prefixIcon('heroicon-m-banknotes'),
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
                    ]),
            ]);
    }
}
