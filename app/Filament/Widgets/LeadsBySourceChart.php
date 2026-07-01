<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Str;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class LeadsBySourceChart extends ApexChartWidget
{
    use HasWidgetShield;
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'leadsBySource';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 6;

    protected ?string $pollingInterval = '30s';

    public function getHeading(): ?string
    {
        return 'Top lead sources';
    }

    public function getSubheading(): ?string
    {
        return 'Where qualified interest is coming from';
    }

    protected function getOptions(): array
    {
        $period = $this->period();

        $counts = $this->leadsBetween($period['start'], $period['end'])
            ->reorder()
            ->get(['source'])
            ->groupBy(fn ($lead) => $lead->source ?: 'unknown')
            ->map
            ->count()
            ->sortDesc()
            ->take(8);

        $labels = $counts->keys()->map(fn (string $source): string => Str::headline($source))->all();
        $values = $counts->values()->all();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'fontFamily' => 'inherit',
                'toolbar' => ['show' => false],
            ],
            'series' => [['name' => 'Leads', 'data' => $values]],
            'xaxis' => [
                'categories' => $labels,
                'axisBorder' => ['show' => false],
                'labels' => ['style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']],
            ],
            'yaxis' => ['labels' => ['style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']]],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'borderRadius' => 4,
                    'borderRadiusApplication' => 'end',
                    'barHeight' => '62%',
                    'distributed' => true,
                ],
            ],
            'colors' => ['#f97316', '#3b82f6', '#6366f1', '#22c55e', '#f59e0b', '#ec4899', '#14b8a6', '#a1a1aa'],
            'dataLabels' => ['enabled' => true, 'style' => ['fontFamily' => 'inherit', 'colors' => ['#ffffff']]],
            'legend' => ['show' => false],
            'grid' => ['borderColor' => '#e4e4e7', 'strokeDashArray' => 4],
            'tooltip' => ['y' => ['title' => ['formatter' => null]]],
            'noData' => ['text' => 'No leads in this period'],
        ];
    }
}
