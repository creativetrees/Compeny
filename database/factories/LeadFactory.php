<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'budget' => fake()->randomElement(['< $10k', '$10k–$25k', '$25k–$50k', '$50k+']),
            'service_interest' => fake()->randomElement([
                'Product Strategy', 'UX & UI Design', 'Web Engineering', 'Mobile Apps',
            ]),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(['new', 'contacted', 'qualified', 'won', 'lost']),
            'source' => 'website',
            'meta' => null,
        ];
    }
}
