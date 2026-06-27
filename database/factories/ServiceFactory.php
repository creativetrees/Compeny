<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->randomElement([
            'Product Strategy', 'UX & UI Design', 'Web Engineering',
            'Mobile Apps', 'Design Systems', 'Platform & DevOps',
        ]);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'icon' => 'heroicon-o-sparkles',
            'summary' => fake()->sentence(10),
            'description' => fake()->paragraphs(2, true),
            'capabilities' => fake()->randomElements(
                ['Discovery', 'Prototyping', 'Frontend', 'Backend', 'QA', 'Analytics'],
                3
            ),
            'is_featured' => true,
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
