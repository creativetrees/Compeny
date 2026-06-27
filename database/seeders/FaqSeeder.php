<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['How quickly can we start?', 'Most engagements kick off within one to two weeks of a signed scope. Discovery sprints can often start sooner.'],
            ['Do you work fixed-price or monthly?', 'Both. A Build engagement is fixed-scope; an Embedded team is month-to-month. We will recommend whichever fits the work.'],
            ['Who owns the code and IP?', 'You do — from the very first commit. Code, accounts, and intellectual property are yours, with no lock-in.'],
            ['Will we work with senior people or juniors?', 'You work directly with the senior team that does the work. No account layers, no handoffs to a B-team.'],
            ['What if we are not sure what we need yet?', 'Start with a two-week Discovery sprint. We will pressure-test the problem and give you a clear, honest recommendation — even if that is "do not build it yet".'],
        ];

        foreach ($faqs as $i => [$question, $answer]) {
            Faq::updateOrCreate(['question' => $question], [
                'answer' => $answer,
                'is_published' => true,
                'sort' => $i,
            ]);
        }
    }
}
