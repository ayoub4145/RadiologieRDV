<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creneaux;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;
use App\Models\Service;
use Carbon\Carbon;


class CreneauxController extends Controller
{
public function getByService(Request $request, $service_id)
{
    try {
        $isUrgent = $request->query('urgent') == '1';

        $service = Service::findOrFail($service_id);

        $query = Creneaux::where('service_id', $service_id);

        if ($isUrgent) {
            $query->whereBetween('date', [now()->toDateString(), now()->addDay()->toDateString()]);
        } else {
            $query->where('is_available', true)
                  ->where('date', '>=', now()->toDateString())
                  ->where('time', '>', now()->format('H:i'));
        }

        $creneaux = $query->orderBy('date')->orderBy('time')->get();

        $creneaux->transform(function ($creneau) use ($service) {
            $duree = $service->duree;

            $debut = \Carbon\Carbon::createFromFormat('H:i:s', $creneau->time);
            $fin = $debut->copy()->addMinutes($duree);

            $creneau->end_time = $fin->format('H:i');

            return $creneau;
        });

        return response()->json($creneaux);

    } catch (\Exception $e) {
        // Pour debug : retourne le message d'erreur en JSON
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function getCreneauxDisponibles($serviceId, Request $request)
{
    $isUrgent = $request->query('urgent', 0);

    $query = Creneaux::where('service_id', $serviceId)
        ->where('disponible', 1);  // Exemple, adapte selon ta colonne disponibilité

    if ($isUrgent) {
        // Si urgent, filtrer sur les créneaux des prochaines 24h
        $now = Carbon::now();
        $in24h = $now->copy()->addDay();

        $query->whereBetween('date', [$now->toDateString(), $in24h->toDateString()]);
        // Ou si tu as un datetime dans creneau, adapte la condition
        // Ex: where('date_heure', '>=', $now)->where('date_heure', '<=', $in24h)
    } else {
        // Sinon, par défaut tu peux afficher les créneaux futurs (plus loin)
        $query->where('date', '>=', Carbon::now()->toDateString());
    }

    $creneaux = $query->orderBy('date')->orderBy('time')->get();

    return response()->json($creneaux);
}

}
