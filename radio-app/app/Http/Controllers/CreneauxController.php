<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creneaux;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;
use App\Models\Service;

class CreneauxController extends Controller
{
public function getByService(Request $request, $service_id)
{
    $isUrgent = $request->query('urgent') == '1';

    // Récupérer le service (avec sa durée en minutes)
    $service = Service::findOrFail($service_id);

    $query = Creneaux::where('service_id', $service_id);

    if ($isUrgent) {
        // Pour urgent : créneaux dans les prochaines 24h, même occupés
        $query->whereBetween('date', [now()->toDateString(), now()->addDay()->toDateString()]);
    } else {
        // Sinon, uniquement créneaux libres
        $query->where('is_available', true)->where('date', '>=', now()->toDateString())->where('time', '>', now()->format('H:i:s'));
    }

    $creneaux = $query->orderBy('date')->orderBy('time')->get();

    // Ajouter l'heure de fin calculée pour chaque créneau
    $creneaux->transform(function ($creneau) use ($service) {
        // Heure début = $creneau->time (format HH:MM:SS)
        // Durée du service en minutes
        $duree = $service->duree; // ex: 45

        // Calcul heure fin = heure début + durée
        $debut = \Carbon\Carbon::createFromFormat('H:i:s', $creneau->time);
        $fin = $debut->copy()->addMinutes($duree);

        // Ajouter une propriété dynamique "end_time" formatée HH:mm
        $creneau->end_time = $fin->format('H:i');

        return $creneau;
    });

    return response()->json($creneaux);
}


    // public function index()
    // {
    //     $creneaux = Creneaux::where('is_available', true)->get();

    //     return view('creneaux.index', compact('creneaux'));
    // }
    // public function reserver(Request $request)
    // {
    //     $request->validate([
    //         'creneau_id' => 'required|exists:creneaux,id',
    //     ]);

    //     $creneau = Creneaux::findOrFail($request->creneau_id);

    //     if (!$creneau->is_available) {
    //         return back()->with('error', 'Ce créneau n\'est plus disponible.');
    //     }

    //     // Créer le rendez-vous
    //     RendezVous::create([
    //         'user_id' => Auth::id(),
    //         'service_id' => $creneau->service_id,
    //         'date_heure' => $creneau->date . ' ' . $creneau->time,
    //         'is_urgent' => false,
    //         'statut' => 'confirmé',
    //     ]);

    //     // Marquer le créneau comme réservé
    //     $creneau->update(['is_available' => false]);

    //     return redirect()->route('dashboard')->with('success', 'Rendez-vous confirmé.');    }
}
