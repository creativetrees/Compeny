<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Realistic demo data for the studio dashboard.
 *
 * Generates ~6 months of leads with an aging-aware status mix (recent leads
 * skew "new", older leads resolve to "won"/"lost") plus showcase content so
 * every widget and chart renders populated. Safe to re-run: it only seeds
 * tables that are (near) empty.
 *
 *   php artisan db:seed --class=DemoDashboardSeeder
 */
class DemoDashboardSeeder extends Seeder
{
    private const DAYS = 180;

    private const SOURCE_WEIGHTS = [
        'website' => 45,
        'referral' => 15,
        'instagram' => 15,
        'ads' => 15,
        'linkedin' => 10,
    ];

    public function run(): void
    {
        $this->seedLeads();
        $this->seedShowcase();

        $this->command?->info('Demo dashboard data ready — '.Lead::count().' leads total.');
    }

    private function seedLeads(): void
    {
        if (Lead::count() >= 120) {
            $this->command?->warn('Leads already populated ('.Lead::count().') — skipping lead seed.');

            return;
        }

        $created = 0;

        for ($daysAgo = self::DAYS; $daysAgo >= 0; $daysAgo--) {
            $day = now()->subDays($daysAgo);

            foreach (range(1, $this->leadsForDay($day)) as $ignored) {
                $timestamp = $day->copy()->setTime(random_int(8, 20), random_int(0, 59), random_int(0, 59));

                Lead::factory()->create([
                    'status' => $this->statusForAge($daysAgo),
                    'source' => $this->weightedPick(self::SOURCE_WEIGHTS),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $created++;
            }
        }

        $this->command?->info("Seeded {$created} demo leads across ".self::DAYS.' days.');
    }

    private function seedShowcase(): void
    {
        if (Client::count() === 0) {
            Client::factory()->count(8)->create();
        }

        if (Project::count() === 0) {
            Project::factory()->count(9)->create(['status' => 'published']);
            Project::factory()->count(3)->create(['status' => 'draft']);
        }

        if (Testimonial::count() === 0) {
            Testimonial::factory()->count(10)->create();
        }

        if (Service::count() === 0) {
            // ServiceFactory draws from a fixed set of 6 unique titles.
            Service::factory()->count(6)->create();
        }
    }

    /** Daily lead volume with a weekday bias and the occasional campaign spike. */
    private function leadsForDay(Carbon $day): int
    {
        $count = random_int(0, 2);

        if ($day->isWeekday()) {
            $count += random_int(0, 1);
        }

        if (random_int(1, 12) === 1) {
            $count += random_int(2, 4);
        }

        return max(1, $count);
    }

    /** Older leads have had time to resolve; recent ones are still fresh. */
    private function statusForAge(int $daysAgo): string
    {
        $weights = match (true) {
            $daysAgo <= 7 => ['new' => 55, 'contacted' => 30, 'qualified' => 15],
            $daysAgo <= 30 => ['new' => 20, 'contacted' => 28, 'qualified' => 22, 'won' => 15, 'lost' => 15],
            $daysAgo <= 90 => ['contacted' => 12, 'qualified' => 22, 'won' => 34, 'lost' => 32],
            default => ['qualified' => 10, 'won' => 45, 'lost' => 45],
        };

        return $this->weightedPick($weights);
    }

    /**
     * @param  array<string, int>  $weights
     */
    private function weightedPick(array $weights): string
    {
        $roll = random_int(1, array_sum($weights));
        $cursor = 0;

        foreach ($weights as $key => $weight) {
            $cursor += $weight;

            if ($roll <= $cursor) {
                return $key;
            }
        }

        return (string) array_key_first($weights);
    }
}
