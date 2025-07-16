<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\TypeInfo;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
         $this->call([
                UserSeeder::class,
                ServiceSeeder::class,
                RendezVousSeeder::class,
                ServiceAvailabilitySeeder::class,
    ]);
            // Création de 4 sections fixes (Blog, Coordonnées, Actualités, Événements)
        $sections = ['Blog', 'Articles',];

        foreach ($sections as $nom) {
            $section = Section::create([
                'name' => $nom,
                'description' => "Description de la section $nom",
            ]);

            // Crée aléatoirement 5 infos par section
            TypeInfo::factory()->count(2)->create([
                'section_id' => $section->id,
            ]);
        }
    }
}
