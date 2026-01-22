<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'payment_method' => fake()->randomElement(['Cash', 'Transfer', 'Online', 'COD']),
            'order_status' => fake()->randomElement(['Pending', 'Confirmed', 'Shipped', 'Cancelled']),
            'total_value' => fake()->randomFloat(2, 100, 5000),
            'notes' => fake()->sentence(),
        ];
    }
}
