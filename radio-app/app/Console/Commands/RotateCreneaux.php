<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use App\Models\Creneaux;
use Carbon\Carbon;

class RotateCreneaux extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rotate-creneaux';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
 public function handle()
    {
        $today = Carbon::today();
        $nowTime = Carbon::now()->format('H:i');

        $services = Service::all();

        foreach ($services as $service) {
            // 1. 🔥 Supprimer les créneaux passés d'aujourd'hui si l'heure est dépassée
            Creneaux::where('service_id', $service->id)
                ->where(function ($q) use ($today, $nowTime) {
                    $q->where('date', '<', $today)
                      ->orWhere(function ($sub) use ($today, $nowTime) {
                          $sub->where('date', $today)
                              ->where('time', '<', $nowTime);
                      });
                })
                ->delete();

            // 2. ➕ Générer les nouveaux créneaux pour le jour J+60
            $date = Carbon::today()->addDays(60);
            $jour = ucfirst($date->locale('fr_FR')->dayName);
            $start = Carbon::parse($date->format('Y-m-d') . ' 08:00');
            $end = Carbon::parse($date->format('Y-m-d') . ' 18:00');

            while ($start->copy()->addMinutes($service->duree)->lte($end)) {
                Creneaux::create([
                    'service_id' => $service->id,
                    'is_available' => true,
                    'day' => $jour,
                    'date' => $date->toDateString(),
                    'time' => $start->format('H:i'),
                ]);
                $start->addMinutes($service->duree);
            }
        }

        $this->info('Rotation des créneaux terminée avec succès.');
    }
}
