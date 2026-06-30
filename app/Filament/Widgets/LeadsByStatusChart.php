<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\InteractsWithDashboardFilters;
use App\Models\Lead;
use Illuminate\Support\Str;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class LeadsByStatusChart extends ApexChartWidget
{
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'leadsByStatus';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 4;

    protected ?string $pollingInterval = '5s';

    public function getHeading(): ?string
    {
        return 'Pipeline by status';
    }

    public function getSubheading(): ?string
    {
        return 'Distribution of leads in the selected period';
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

        $labels = [];
        $series = [];
        $colors = [];

        foreach (Lead::STATUSES as $status) {
            $value = (int) ($counts[$status] ?? 0);

            if ($value === 0) {
                continue;
            }

            $labels[] = Str::headline($status);
            $series[] = $value;
            $colors[] = self::STATUS_COLORS[$status];
        }

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
                'fontFamily' => 'inherit',
            ],
            'series' => $series,
            'labels' => $labels,
            'colors' => $colors,
            'stroke' => ['width' => 2, 'colors' => ['#ffffff']],
            'dataLabels' => ['enabled' => true, 'style' => ['fontFamily' => 'inherit']],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '68%',
                        'labels' => [
                            'show' => true,
                            'total' => [
                                'show' => true,
                                'label' => 'Total leads',
                                'fontFamily' => 'inherit',
                                'color' => '#71717a',
                            ],
                        ],
                    ],
                ],
            ],
            'legend' => ['position' => 'bottom', 'fontFamily' => 'inherit'],
            'noData' => ['text' => 'No leads in this period'],
        ];
    }
}
