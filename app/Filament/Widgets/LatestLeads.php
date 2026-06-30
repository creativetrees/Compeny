<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LatestLeads extends TableWidget
{
    use InteractsWithDashboardFilters;

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 6;

    public function table(Table $table): Table
    {
        $period = $this->period();

        return $table
            ->query($this->leadsBetween($period['start'], $period['end']))
            ->heading('Latest leads')
            ->description('Newest inbound · '.$period['label'])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->poll('5s')
            ->columns([
                TextColumn::make('name')
                    ->label('Lead')
                    ->weight('bold')
                    ->description(fn (Lead $record): ?string => $record->email)
                    ->searchable(),
                TextColumn::make('company')
                    ->icon('heroicon-m-building-office-2')
                    ->placeholder('Independent')
                    ->toggleable(),
                TextColumn::make('service_interest')
                    ->label('Interest')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
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
                TextColumn::make('source')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => Str::headline($state ?: 'unknown'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (Model $record): string => LeadResource::getUrl('edit', ['record' => $record]));
    }
}
