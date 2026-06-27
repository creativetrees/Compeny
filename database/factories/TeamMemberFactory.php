<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'role' => fake()->randomElement([
                'Founder & CEO', 'Design Lead', 'Engineering Lead',
                'Product Manager', 'Senior Engineer', 'Brand Designer',
            ]),
            'bio' => fake()->paragraph(),
            'photo_path' => 'https://i.pravatar.cc/480?u='.Str::slug($name),
            'socials' => ['linkedin' => 'https://linkedin.com', 'x' => 'https://x.com'],
            'is_published' => true,
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
