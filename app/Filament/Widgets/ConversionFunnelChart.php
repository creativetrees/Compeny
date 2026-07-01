<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ConversionFunnelChart extends ApexChartWidget
{
    use HasWidgetShield;
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'conversionFunnel';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 6;

    protected ?string $pollingInterval = '30s';

    public function getHeading(): ?string
    {
        return 'Conversion funnel';
    }

    public function getSubheading(): ?string
    {
        return 'How leads progress from inbox to won';
    }

    protected function getOptions(): array
    {
        $period = $this->period();

        $counts = $this->leadsBetween($period['start'], $period['end'])
            ->reorder()
            ->get(['status'])
            ->groupBy('status')
            ->map
            ->count();

        $new = (int) ($counts['new'] ?? 0);
        $contacted = (int) ($counts['contacted'] ?? 0);
        $qualified = (int) ($counts['qualified'] ?? 0);
        $won = (int) ($counts['won'] ?? 0);
        $lost = (int) ($counts['lost'] ?? 0);

        // Cumulative pipeline: every lead enters, then progresses stage by stage.
        $entered = $new + $contacted + $qualified + $won + $lost;
        $reachedContacted = $contacted + $qualified + $won;
        $reachedQualified = $qualified + $won;

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'fontFamily' => 'inherit',
                'toolbar' => ['show' => false],
            ],
            'series' => [[
                'name' => 'Leads',
                'data' => [$entered, $reachedContacted, $reachedQualified, $won],
            ]],
            'xaxis' => [
                'categories' => ['All leads', 'Contacted', 'Qualified', 'Won'],
                'labels' => ['style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']],
            ],
            'yaxis' => ['labels' => ['style' => ['fontFamily' => 'inherit', 'colors' => '#71717a']]],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'borderRadius' => 3,
                    'barHeight' => '70%',
                    'distributed' => true,
                    'isFunnel' => true,
                ],
            ],
            'colors' => ['#3b82f6', '#f59e0b', '#6366f1', '#22c55e'],
            'dataLabels' => [
                'enabled' => true,
                'style' => ['fontFamily' => 'inherit', 'colors' => ['#ffffff'], 'fontWeight' => 600],
            ],
            'legend' => ['show' => false],
            'grid' => ['show' => false],
            'noData' => ['text' => 'No leads in this period'],
        ];
    }
}
