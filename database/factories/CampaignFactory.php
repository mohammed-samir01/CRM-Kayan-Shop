<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->catchPhrase(),
            'platform' => fake()->randomElement(['TikTok', 'Facebook', 'Instagram', 'Google', 'Snapchat', 'X', 'YouTube', 'LinkedIn', 'Other']),
            'ad_type' => fake()->randomElement(['Video', 'Image', 'Carousel', 'Search', 'Story', 'Other']),
            'source' => fake()->randomElement(['Form', 'WhatsApp', 'Phone Call', 'Website', 'DM', 'Other']),
            'notes' => fake()->sentence(),
        ];
    }
}
