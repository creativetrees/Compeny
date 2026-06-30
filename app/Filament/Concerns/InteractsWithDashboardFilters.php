<?php

namespace App\Filament\Concerns;

use App\Models\Lead;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Shared dashboard period handling.
 *
 * Every dashboard widget reads the active range from the page-level "period"
 * filter (Last 7 / 30 / 90 days, Last 12 months, All time). Charts also get a
 * matching "previous period" window so they can show trends and comparisons.
 */
trait InteractsWithDashboardFilters
{
    use InteractsWithPageFilters;

    /** Brand-aligned, semantic colours shared by every chart (matches the Leads table badges). */
    public const STATUS_COLORS = [
        'new' => '#3b82f6',        // blue  — info
        'contacted' => '#f59e0b',  // amber — warning
        'qualified' => '#6366f1',  // indigo — primary
        'won' => '#22c55e',        // green — success
        'lost' => '#ef4444',       // red   — danger
    ];

    public const ACCENT = '#f97316'; // brand orange

    /**
     * Resolve the active dashboard period into concrete date windows.
     *
     * @return array{key:string, label:string, days:?int, start:?Carbon, end:Carbon, prevStart:?Carbon, prevEnd:?Carbon}
     */
    protected function period(): array
    {
        $key = (string) ($this->pageFilters['period'] ?? '30');
        $end = now();

        $days = match ($key) {
            '7' => 7,
            '90' => 90,
            '365' => 365,
            'all' => null,
            default => 30,
        };

        $label = match ($key) {
            '7' => 'Last 7 days',
            '90' => 'Last 90 days',
            '365' => 'Last 12 months',
            'all' => 'All time',
            default => 'Last 30 days',
        };

        if ($days === null) {
            return [
                'key' => 'all',
                'label' => $label,
                'days' => null,
                'start' => null,
                'end' => $end,
                'prevStart' => null,
                'prevEnd' => null,
            ];
        }

        $start = $end->copy()->subDays($days - 1)->startOfDay();

        return [
            'key' => $days === 30 ? '30' : $key,
            'label' => $label,
            'days' => $days,
            'start' => $start,
            'end' => $end,
            'prevStart' => $start->copy()->subDays($days),
            'prevEnd' => $start->copy()->subSecond(),
        ];
    }

    /** A Lead query bounded to the given window (open-ended when a bound is null). */
    protected function leadsBetween(?Carbon $start, ?Carbon $end): Builder
    {
        $query = Lead::query();

        if ($start) {
            $query->where('created_at', '>=', $start);
        }

        if ($end) {
            $query->where('created_at', '<=', $end);
        }

        return $query;
    }

    /**
     * Per-day lead counts across an inclusive window, with zero-filled gaps.
     * Bucketed in PHP so the query stays database-agnostic (Postgres/SQLite).
     *
     * @return array<string, int> keyed by Y-m-d
     */
    protected function dailyLeadCounts(Carbon $start, Carbon $end, ?string $status = null): array
    {
        $query = $this->leadsBetween($start, $end);

        if ($status) {
            $query->where('status', $status);
        }

        $counts = $query->reorder()->get(['created_at'])
            ->groupBy(fn (Lead $lead): string => $lead->created_at->format('Y-m-d'))
            ->map->count();

        $series = [];

        for ($day = $start->copy()->startOfDay(); $day->lte($end); $day->addDay()) {
            $key = $day->format('Y-m-d');
            $series[$key] = (int) ($counts[$key] ?? 0);
        }

        return $series;
    }

    /** Earliest lead timestamp, used as the natural start for "All time" charts. */
    protected function earliestLeadDate(): ?Carbon
    {
        $earliest = Lead::min('created_at');

        return $earliest ? Carbon::parse($earliest) : null;
    }

    /** Percentage change between two totals, rounded; null when there is no baseline. */
    protected function trend(int|float $current, int|float $previous): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
