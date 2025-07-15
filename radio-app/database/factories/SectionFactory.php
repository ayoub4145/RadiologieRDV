<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Exemple de noms possibles
        $sections = ['Blog', 'Coordonnées', 'Actualités', 'Articles','FAQ','temoignages','A propos','Services','Équipe'];

        return [
            'name' => $this->faker->unique()->randomElement($sections),
            'description' => $this->faker->sentence(),
        ];

    }
}
