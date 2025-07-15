<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Section;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Type_info>
 */
class TypeInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
protected $model = \App\Models\TypeInfo::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(), // Crée une section associée si non précisé
            'titre' => $this->faker->sentence(3),
            'contenu' => $this->faker->paragraph(),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'business'),
            'numero' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'lien' => $this->faker->optional()->url(),
            'ordre' => $this->faker->numberBetween(1, 10),
            'is_active' => $this->faker->boolean(90), // 90% chance true
        ];
    }
}
