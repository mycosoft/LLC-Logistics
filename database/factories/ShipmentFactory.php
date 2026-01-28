<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'origin' => fake()->city(),
            'destination' => fake()->city(),
            'weight' => fake()->randomFloat(2, 1, 100),
            'shipment_type' => fake()->randomElement(['air', 'sea', 'road']),
            'current_status' => 'Pending',
            'description' => fake()->sentence(),
            'expected_delivery_date' => fake()->dateTimeBetween('now', '+1 month'),
            'shipping_cost' => fake()->randomFloat(2, 10, 500),
            'payment_status' => 'pending',
        ];
    }
}
