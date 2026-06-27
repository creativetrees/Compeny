<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        $author = fake()->name();

        return [
            'project_id' => null,
            'author' => $author,
            'role' => fake()->jobTitle(),
            'company' => fake()->company(),
            'quote' => fake()->paragraph(),
            'avatar_path' => 'https://i.pravatar.cc/200?u='.Str::slug($author),
            'rating' => 5,
            'is_featured' => fake()->boolean(60),
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
