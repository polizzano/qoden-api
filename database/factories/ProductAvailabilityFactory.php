<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAvailability>
 */
class ProductAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => $this->faker->randomElement(['ProviderA', 'ProviderB', 'ProviderC']),
            'product_type' => $this->faker->randomElement(['data', 'voip', 'voice']),
            'speed' => $this->faker->numberBetween(10, 1000),
            'address' => $this->faker->address,
            'updated_at' => now(),
        ];
    }
}
