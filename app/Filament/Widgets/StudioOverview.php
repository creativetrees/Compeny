<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Service;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudioOverview extends StatsOverviewWidget
{
    use HasWidgetShield;
    use InteractsWithDashboardFilters;

    protected ?string $pollingInterval = '30s';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return 'Studio overview';
    }

    protected function getDescription(): ?string
    {
        return $this->period()['label'].' · live';
    }

    protected function getStats(): array
    {
        $period = $this->period();
        $start = $period['start'];
        $end = $period['end'];
        $hasPrev = $period['prevStart'] !== null;

        $total = $this->leadsBetween($start, $end)->count();
        $totalPrev = $hasPrev ? $this->leadsBetween($period['prevStart'], $period['prevEnd'])->count() : 0;

        $won = $this->leadsBetween($start, $end)->where('status', 'won')->count();
        $wonPrev = $hasPrev ? $this->leadsBetween($period['prevStart'], $period['prevEnd'])->where('status', 'won')->count() : 0;

        $qualified = $this->leadsBetween($start, $end)->where('status', 'qualified')->count();
        $qualifiedPrev = $hasPrev ? $this->leadsBetween($period['prevStart'], $period['prevEnd'])->where('status', 'qualified')->count() : 0;

        $newBacklog = Lead::where('status', 'new')->count();

        $conversion = $total > 0 ? round(($won / $total) * 100, 1) : 0.0;
        $conversionPrev = $totalPrev > 0 ? round(($wonPrev / $totalPrev) * 100, 1) : 0.0;

        $sparkStart = $start ?? ($this->earliestLeadDate() ?? $end->copy()->subDays(29));
        $totalSeries = array_values($this->dailyLeadCounts($sparkStart, $end));
        $wonSeries = array_values($this->dailyLeadCounts($sparkStart, $end, 'won'));

        return [
            $this->trendStat('Total leads', $total, $totalSeries, $this->trend($total, $totalPrev), $hasPrev)
                ->descriptionIcon('heroicon-m-inbox-stack'),

            Stat::make('New', $newBacklog)
                ->description('Awaiting first response')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($newBacklog > 0 ? 'info' : 'gray'),

            $this->trendStat('Qualified', $qualified, [], $this->trend($qualified, $qualifiedPrev), $hasPrev)
                ->descriptionIcon('heroicon-m-check-badge'),

            $this->trendStat('Won', $won, $wonSeries, $this->trend($won, $wonPrev), $hasPrev)
                ->color('success')
                ->chartColor('success'),

            $this->trendStat('Conversion rate', $conversion.'%', [], $this->trend($conversion, $conversionPrev), $hasPrev)
                ->descriptionIcon('heroicon-m-bolt'),

            Stat::make('Published work', Project::published()->count())
                ->description(Service::where('is_featured', true)->count().' featured services live')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('gray'),
        ];
    }

    /**
     * Build a stat whose description encodes the period-over-period trend.
     *
     * @param  array<int, int>  $chart
     */
    private function trendStat(string $label, int|string $value, array $chart, ?float $trend, bool $hasPrev): Stat
    {
        $stat = Stat::make($label, $value);

        if ($chart !== []) {
            $stat->chart($chart);
        }

        if (! $hasPrev || $trend === null) {
            return $stat
                ->description('No prior period')
                ->descriptionIcon('heroicon-m-minus-small')
                ->descriptionColor('gray');
        }

        $up = $trend >= 0;

        return $stat
            ->description(($up ? '+' : '').$trend.'% vs previous')
            ->descriptionIcon($up ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->descriptionColor($up ? 'success' : 'danger')
            ->color($up ? 'success' : 'danger');
    }
}
