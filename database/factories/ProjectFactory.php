<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();
        $seed = fake()->unique()->numberBetween(1, 9999);

        return [
            'category_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.$seed,
            'client_name' => fake()->company(),
            'year' => fake()->numberBetween(2022, 2026),
            'role' => fake()->randomElement(['Design & Engineering', 'Product & Design', 'End-to-end build']),
            'summary' => fake()->sentence(14),
            'body' => fake()->paragraphs(4, true),
            'cover_path' => 'https://picsum.photos/seed/ctw-'.$seed.'/1280/860',
            'gallery' => [
                'https://picsum.photos/seed/ctg1-'.$seed.'/1280/860',
                'https://picsum.photos/seed/ctg2-'.$seed.'/1280/860',
            ],
            'services' => fake()->randomElements(['Strategy', 'Design', 'Engineering', 'Branding'], 3),
            'results' => [
                ['label' => 'Conversion', 'value' => '+'.fake()->numberBetween(20, 80).'%'],
                ['label' => 'Launch', 'value' => fake()->numberBetween(6, 16).' wks'],
            ],
            'website_url' => fake()->url(),
            'is_featured' => fake()->boolean(50),
            'status' => 'published',
            'sort' => fake()->numberBetween(0, 40),
        ];
    }
}
