<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'SaaS', 'Fintech', 'Healthtech', 'Marketplace', 'AI / ML', 'Developer Tools',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'type' => 'project',
            'description' => fake()->sentence(),
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
