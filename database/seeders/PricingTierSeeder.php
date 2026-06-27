<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;

class PricingTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Sprint',
                'term' => '2-week engagement',
                'price_label' => 'From',
                'price' => '$6K',
                'suffix' => null,
                'tagline' => 'A focused two-week sprint to pressure-test an idea before you commit real budget to building it.',
                'items' => [
                    'Problem, market & risk framing',
                    'Core user flows mapped end to end',
                    'A clickable, testable prototype',
                    'Technical feasibility review',
                    'A clear go / no-go recommendation',
                ],
                'is_featured' => false,
                'sort' => 0,
            ],
            [
                'name' => 'Build',
                'term' => 'Fixed scope',
                'price_label' => 'From',
                'price' => '$25K',
                'suffix' => null,
                'tagline' => 'Design and engineering working as one team to ship a focused, production-ready v1 — fast.',
                'items' => [
                    'Product & interface design',
                    'Typed, tested front and back end',
                    'CI/CD with a live environment',
                    'Analytics instrumented from day one',
                    'Weekly demos — shippable every week',
                ],
                'is_featured' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Embedded',
                'term' => 'Month-to-month',
                'price_label' => 'From',
                'price' => '$18K',
                'suffix' => '/ mo',
                'tagline' => 'A dedicated product team that plugs into your roadmap and scales up or down as you go.',
                'items' => [
                    'A cross-functional design + eng pod',
                    'Continuous delivery on your roadmap',
                    'Fixed monthly cost, no surprise invoices',
                    'A direct line to the team in Slack',
                    'Month-to-month — scale up or down',
                ],
                'is_featured' => false,
                'sort' => 2,
            ],
        ];

        foreach ($tiers as $tier) {
            PricingTier::updateOrCreate(['name' => $tier['name']], $tier);
        }
    }
}
