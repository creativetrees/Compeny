<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TopServicesChart extends ApexChartWidget
{
    use HasWidgetShield;
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'topServices';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 6;

    protected ?string $pollingInterval = '30s';

    public function getHeading(): ?string
    {
        return 'Most requested services';
    }

    public function getSubheading(): ?string
    {
        return 'What leads are asking us to build';
    }

    protected function getOptions(): array
    {
        $period = $this->period();

        $counts = $this->leadsBetween($period['start'], $period['end'])
            ->whereNotNull('service_interest')
            ->where('service_interest', '!=', '')
            ->reorder()
            ->get(['service_interest'])
            ->groupBy('service_interest')
            ->map
            ->count()
            ->sortDesc()
            ->take(8);

        $labels = $counts->keys()->all();
        $values = $counts->values()->all();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'fontFamily' => 'inherit',
                'toolbar' => ['show' => false],
            ],
            'series' => [['name' => 'Requests', 'data' => $values]],
            'xaxis' => [
                'categories' => $labels,
                'axisBorder' => ['show' => false],
                'labels' => ['rotate' => -30, 'hideOverlappingLabels' => true, 'trim' => true, 'style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']],
            ],
            'yaxis' => ['min' => 0, 'forceNiceScale' => true, 'labels' => ['style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']]],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'borderRadius' => 4,
                    'borderRadiusApplication' => 'end',
                    'columnWidth' => '55%',
                    'distributed' => true,
                ],
            ],
            'colors' => ['#f97316', '#6366f1', '#3b82f6', '#22c55e', '#f59e0b', '#ec4899', '#14b8a6', '#a1a1aa'],
            'dataLabels' => ['enabled' => true, 'style' => ['fontFamily' => 'inherit', 'colors' => ['#3f3f46']], 'offsetY' => -18],
            'legend' => ['show' => false],
            'grid' => ['borderColor' => '#e4e4e7', 'strokeDashArray' => 4],
            'noData' => ['text' => 'No service interest recorded in this period'],
        ];
    }
}
