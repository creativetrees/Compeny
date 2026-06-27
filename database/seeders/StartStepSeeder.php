<?php

namespace Database\Seeders;

use App\Models\StartStep;
use Illuminate\Database\Seeder;

class StartStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            ['Reply within a day', 'A real person reads every brief and responds within one business day.', 0],
            ['A short call', 'We talk through the problem, the timeline, and whether we are the right fit.', 1],
            ['A clear proposal', 'Scope, price, and a start date — in plain language, no surprises.', 2],
        ];

        foreach ($steps as [$title, $description, $sort]) {
            StartStep::updateOrCreate(['title' => $title], [
                'description' => $description,
                'sort' => $sort,
            ]);
        }
    }
}
