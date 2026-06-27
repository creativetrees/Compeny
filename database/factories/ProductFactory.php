<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();

        return [
            'category_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'type' => fake()->randomElement(['SaaS', 'Template', 'Service']),
            'summary' => fake()->sentence(12),
            'description' => fake()->paragraphs(2, true),
            'price_label' => 'From $'.fake()->numberBetween(2, 20).',000',
            'features' => fake()->randomElements(
                ['Auth', 'Billing', 'Dashboard', 'API', 'Analytics', 'Admin'],
                4
            ),
            'cover_path' => 'https://picsum.photos/seed/ctp-'.fake()->unique()->numberBetween(1, 9999).'/1200/800',
            'cta_label' => 'Request access',
            'cta_url' => null,
            'is_featured' => fake()->boolean(40),
            'status' => 'published',
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
