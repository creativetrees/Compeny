<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contact')->icon('heroicon-m-user')->columns(2)->schema([
                TextEntry::make('name')->icon('heroicon-m-user')->weight('bold'),
                TextEntry::make('email')->label('Email address')->icon('heroicon-m-envelope')->copyable(),
                TextEntry::make('company')->icon('heroicon-m-building-office-2')->placeholder('—'),
                TextEntry::make('phone')->icon('heroicon-m-phone')->copyable()->placeholder('—'),
            ]),
            Section::make('Inquiry')->icon('heroicon-m-chat-bubble-left-right')->columns(2)->schema([
                TextEntry::make('budget')->icon('heroicon-m-banknotes')->placeholder('—'),
                TextEntry::make('service_interest')->label('Service interest')->badge()->placeholder('—'),
                TextEntry::make('message')->html()->prose()->columnSpanFull(),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->icon(fn (string $state): string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'contacted' => 'heroicon-m-chat-bubble-left-right',
                        'qualified' => 'heroicon-m-check-badge',
                        'won' => 'heroicon-m-trophy',
                        'lost' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-flag',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'qualified' => 'primary',
                        'won' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('source')->icon('heroicon-m-globe-alt')->placeholder('—'),
            ]),
            Section::make('Team notification')->icon('heroicon-m-bell-alert')->columns(2)->schema([
                TextEntry::make('notification_status')
                    ->label('Email to team')
                    ->badge()
                    ->state(fn (Lead $record): string => $record->notified_at
                        ? 'Sent'
                        : ($record->notification_error ? 'Failed' : 'Pending'))
                    ->color(fn (Lead $record): string => $record->notified_at
                        ? 'success'
                        : ($record->notification_error ? 'danger' : 'warning'))
                    ->icon(fn (Lead $record): string => $record->notified_at
                        ? 'heroicon-m-check-circle'
                        : ($record->notification_error ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-clock')),
                TextEntry::make('notified_at')->label('Sent at')->since()->placeholder('—'),
                TextEntry::make('notification_error')
                    ->label('Failure reason')
                    ->columnSpanFull()
                    ->visible(fn (Lead $record): bool => filled($record->notification_error))
                    ->helperText('The team email did not go out. Check the mail account in Site Settings → Email addresses, then contact this lead directly.'),
            ]),
            Section::make('Metadata')->icon('heroicon-m-information-circle')->columns(2)->collapsed()->schema([
                TextEntry::make('created_at')->label('Received')->since(),
                TextEntry::make('updated_at')->label('Last updated')->since(),
                KeyValueEntry::make('meta')->columnSpanFull()->keyLabel('Key')->valueLabel('Value'),
            ]),
        ]);
    }
}
