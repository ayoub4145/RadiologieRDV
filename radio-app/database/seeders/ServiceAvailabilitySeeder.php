<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceAvailability;

class ServiceAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $services = Service::all();

        foreach ($services as $service) {
            // On génère 3 créneaux de disponibilité par service
            ServiceAvailability::factory()->count(3)->create([
                'service_id' => $service->id,
            ]);
        }
    }
}
