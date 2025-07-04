<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceAvailability>
 */
class ServiceAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        return [
            'service_id' => Service::inRandomOrder()->first()->id ?? Service::factory()->create()->id,
            'start_day' => $this->faker->randomElement($days),
            'end_day' => $this->faker->randomElement($days),
            'start_time' => $this->faker->time('H:i', '09:00'),
            'end_time' => $this->faker->time('H:i', '17:00'),
        ];
    }
}
