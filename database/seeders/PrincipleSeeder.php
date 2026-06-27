<?php

namespace Database\Seeders;

use App\Models\Principle;
use Illuminate\Database\Seeder;

class PrincipleSeeder extends Seeder
{
    public function run(): void
    {
        $principles = [
            ['Ship weekly', 'Working software in your hands every week — progress you can see and use, not status decks.', 0],
            ['One team, no handoff', 'Strategy, design, and engineering sit in one room. Nothing is lost in translation between them.', 1],
            ['Evidence over opinion', 'Decisions earn their place with data and user signal, never seniority or the loudest voice.', 2],
            ['De-risk early', 'We spend the riskiest assumptions first, while change is still cheap and reversible.', 3],
        ];

        foreach ($principles as [$title, $description, $sort]) {
            Principle::updateOrCreate(['title' => $title], [
                'description' => $description,
                'sort' => $sort,
            ]);
        }
    }
}
