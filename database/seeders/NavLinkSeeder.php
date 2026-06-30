<?php

namespace Database\Seeders;

use App\Models\NavLink;
use Illuminate\Database\Seeder;

class NavLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'header' => [
                ['Work', '/work'],
                ['Services', '/services'],
                ['Pricing', '/pricing'],
                ['Process', '/process'],
                ['Team', '/team'],
                ['About', '/about'],
            ],
            'footer_studio' => [
                ['Work', '/work'],
                ['Services', '/services'],
                ['Process', '/process'],
                ['Pricing', '/pricing'],
            ],
            'footer_company' => [
                ['About', '/about'],
                ['Team', '/team'],
                ['Products', '/products'],
                ['Contact', '/contact'],
                ['Start a project', '/start'],
            ],
        ];

        foreach ($links as $location => $rows) {
            foreach ($rows as $i => [$label, $url]) {
                NavLink::updateOrCreate(
                    ['location' => $location, 'label' => $label],
                    ['url' => $url, 'sort' => $i],
                );
            }
        }
    }
}
