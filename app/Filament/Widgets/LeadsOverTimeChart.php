<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class LeadsOverTimeChart extends ApexChartWidget
{
    use HasWidgetShield;
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'leadsOverTime';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 8;

    protected ?string $pollingInterval = '30s';

    public function getHeading(): ?string
    {
        return 'Leads over time';
    }

    public function getSubheading(): ?string
    {
        return 'Daily inbound — current vs previous period';
    }

    protected function getOptions(): array
    {
        $period = $this->period();
        $end = $period['end'];
        $start = $period['start'] ?? ($this->earliestLeadDate() ?? $end->copy()->subDays(29));

        $current = $this->dailyLeadCounts($start, $end);
        $labels = array_map(
            fn (string $date): string => Carbon::parse($date)->format('M j'),
            array_keys($current),
        );

        $series = [[
            'name' => 'This period',
            'data' => array_values($current),
        ]];

        $colors = [self::ACCENT];
        $width = [3];
        $dashArray = [0];

        if ($period['prevStart']) {
            $series[] = [
                'name' => 'Previous period',
                'data' => array_values($this->dailyLeadCounts($period['prevStart'], $period['prevEnd'])),
            ];
            $colors[] = '#a1a1aa';
            $width[] = 2;
            $dashArray[] = 5;
        }

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'fontFamily' => 'inherit',
                'toolbar' => ['show' => false],
                'zoom' => ['enabled' => false],
                'animations' => ['enabled' => true, 'easing' => 'easeinout', 'speed' => 400],
            ],
            'series' => $series,
            'colors' => $colors,
            'stroke' => ['curve' => 'smooth', 'width' => $width, 'dashArray' => $dashArray],
            'fill' => [
                'type' => 'gradient',
                'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.4, 'opacityTo' => 0.03, 'stops' => [0, 95]],
            ],
            'dataLabels' => ['enabled' => false],
            'xaxis' => [
                'categories' => $labels,
                'tickAmount' => min(10, max(1, count($labels) - 1)),
                'axisBorder' => ['show' => false],
                'axisTicks' => ['show' => false],
                'labels' => ['rotate' => 0, 'hideOverlappingLabels' => true, 'style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']],
            ],
            'yaxis' => [
                'min' => 0,
                'forceNiceScale' => true,
                'labels' => ['style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']],
            ],
            'grid' => ['borderColor' => '#e4e4e7', 'strokeDashArray' => 4, 'padding' => ['left' => 8, 'right' => 8]],
            'markers' => ['size' => 0, 'hover' => ['size' => 5]],
            'legend' => ['show' => count($series) > 1, 'position' => 'top', 'horizontalAlign' => 'right', 'fontFamily' => 'inherit'],
            'tooltip' => ['shared' => true, 'intersect' => false],
        ];
    }
}
