<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Creneaux;
use Carbon\Carbon;

class CreneauxSeeder extends Seeder
{
    public function run(): void
    {
        $jours = 60; // Générer les créneaux pour les 7 prochains jours
        $heureDebut = '08:00';
        $heureFin = '18:00';

        $services = Service::all();

        foreach ($services as $service) {
            for ($j = 0; $j < $jours; $j++) {
                $date = Carbon::now()->addDays($j)->toDateString();
                $jour = Carbon::parse($date)->locale('fr_FR')->dayName;

                $start = Carbon::parse($date . ' ' . $heureDebut);
                $end = Carbon::parse($date . ' ' . $heureFin);

                while ($start->copy()->addMinutes($service->duree)->lte($end)) {
                    Creneaux::create([
                        'service_id' => $service->id,
                        'is_available' => true,
                        'day' => ucfirst($jour),
                        'date' => $date,
                        'time' => $start->format('H:i'),
                    ]);

                    $start->addMinutes($service->duree);
                }
            }
        }
    }
}
