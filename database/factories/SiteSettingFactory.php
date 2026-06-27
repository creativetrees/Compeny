<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand_name' => 'Creative Trees Group',
            'hero_eyebrow' => 'DIGITAL PRODUCT STUDIO & IT ECOSYSTEM',
            'hero_title' => 'WE GROW DIGITAL PRODUCTS THAT SCALE',
            'hero_subtitle' => 'We help startups and teams turn ideas into powerful digital products.',
            'hero_cta_label' => 'Start a project',
            'hero_cta_url' => '/start',
            'contact_email' => 'hello@creativetrees.group',
        ];
    }
}
