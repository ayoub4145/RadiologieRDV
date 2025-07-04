<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_name' => $this->faker->randomElement(['IRM', 'Scanner', 'Échographie', 'Radiographie']),
            'duree' => $this->faker->randomElement([30, 45, 60]),
            'description' => $this->faker->sentence(),
            'tarif' => $this->faker->randomFloat(2, 400, 1000),

        ];
    }
}
