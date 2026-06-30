<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Lead')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Contact')->icon('heroicon-o-user')->columns(2)->schema([
                    TextInput::make('name')->required()->prefixIcon('heroicon-m-user')
                        ->placeholder('Jane Doe')->helperText('Full name of the person enquiring.'),
                    TextInput::make('email')->label('Email address')->email()->required()
                        ->prefixIcon('heroicon-m-envelope')->placeholder('jane@company.com')
                        ->helperText('We reply to this address.'),
                    TextInput::make('company')->prefixIcon('heroicon-m-building-office-2')
                        ->placeholder('Acme Inc.')->helperText('Optional — their organisation.'),
                    TextInput::make('phone')->tel()->prefixIcon('heroicon-m-phone')
                        ->placeholder('+62 812 3456 7890')->helperText('Optional contact number.'),
                ]),
                Tab::make('Inquiry')->icon('heroicon-o-chat-bubble-left-right')->columns(2)->schema([
                    TextInput::make('budget')->prefixIcon('heroicon-m-banknotes')
                        ->placeholder('$50k+')->helperText('Stated budget range, if any.'),
                    TextInput::make('service_interest')->label('Service interest')
                        ->prefixIcon('heroicon-m-squares-2x2')->placeholder('UX & UI Design')
                        ->helperText('What they are interested in.'),
                    Textarea::make('message')->required()->rows(6)->columnSpanFull()
                        ->placeholder('Tell us about the project…')
                        ->helperText('The enquiry message submitted from the site.'),
                ]),
                Tab::make('Internal')->icon('heroicon-o-cog-6-tooth')->columns(2)->schema([
                    Select::make('status')->required()->native(false)->default('new')
                        ->prefixIcon('heroicon-m-flag')
                        ->options(collect(Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => Str::headline($s)])->all())
                        ->helperText('Pipeline stage for this lead.'),
                    TextInput::make('source')->default('website')->disabled()->dehydrated()
                        ->prefixIcon('heroicon-m-globe-alt')->helperText('Where the lead came from.'),
                    KeyValue::make('meta')->columnSpanFull()->keyLabel('Key')->valueLabel('Value')
                        ->helperText('Extra metadata captured with the submission (UTM, IP, …).'),
                    Placeholder::make('created_at')->label('Received')
                        ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '—'),
                    Placeholder::make('updated_at')->label('Last updated')
                        ->content(fn ($record) => $record?->updated_at?->diffForHumans() ?? '—'),
                ]),
            ]),
        ]);
    }
}
