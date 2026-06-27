<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'logo_path' => null,
            'website_url' => fake()->url(),
            'is_featured' => true,
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
