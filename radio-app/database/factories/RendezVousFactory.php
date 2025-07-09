<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Service;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RendezVous>
 */
class RendezVousFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();
            return [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'date_heure' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'is_urgent' => $this->faker->boolean(10),
            // 'statut' => $this->faker->randomElement(['en_attente', 'confirmé', 'annulé']),
            'resultat' => null,
            'commentaire' => $this->faker->optional()->sentence(),
        ];
    }
}
