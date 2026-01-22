<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'status' => fake()->randomElement(['New', 'Contacted', 'Interested', 'Confirmed', 'Shipped', 'Cancelled']),
            'expected_value' => fake()->randomFloat(2, 100, 10000),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'notes' => fake()->sentence(),
        ];
    }
}
