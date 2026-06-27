<?php

namespace Database\Seeders;

use App\Models\PricingInclude;
use Illuminate\Database\Seeder;

class PricingIncludeSeeder extends Seeder
{
    public function run(): void
    {
        $includes = [
            ['Weekly demos', 'You see working software every week — never a status deck.', 0],
            ['Fixed weekly cost', 'Scope can flex. The number you budgeted does not move.', 1],
            ['You own everything', 'Code, accounts, and IP are yours from the first commit.', 2],
            ['No lock-in', 'Month-to-month where it makes sense. Leave whenever you like.', 3],
        ];

        foreach ($includes as [$label, $description, $sort]) {
            PricingInclude::updateOrCreate(['label' => $label], [
                'label' => $label,
                'description' => $description,
                'sort' => $sort,
            ]);
        }
    }
}
