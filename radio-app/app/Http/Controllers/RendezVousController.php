<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;
use App\Models\Service;
use App\Models\Creneaux;
use App\Models\Visiteur;
use Carbon\Carbon;
class RendezVousController extends Controller

{

public function store(Request $request)
{
    $validated = $request->validate([
        'service_id'   => 'required|exists:services,id',
        'date_heure'   => 'required|date|after:now',
        'commentaire'  => 'nullable|string',
        'visiteur_id'  => 'nullable|exists:visiteur,id',
    ]);
    // Vérifie si un visiteur avec cet email existe déjà
    $visiteur = Visiteur::firstOrCreate(
        ['email' => $request->email],
        ['nom' => $request->nom]
    );

    $rendezVous = new RendezVous();
    $rendezVous->user_id     = Auth::id();
    $rendezVous->service_id  = $validated['service_id'];
    $rendezVous->date_heure  = $validated['date_heure'];
    $rendezVous->is_urgent   = $request->has('is_urgent') ? 1 : 0;
    $rendezVous->commentaire = $validated['commentaire'] ?? null;
    // $rendezVous->statut      = $rendezVous->is_urgent ? 'en_attente' : 'confirmé';
    $rendezVous->visiteur_id = $validated['visiteur_id'] ?? $visiteur->id;
    $rendezVous->save();

    // Si c'est une requête AJAX ou JSON, répondre en JSON
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'message' => 'Votre rendez-vous a été enregistré avec succès.',
            'rendezVous' => $rendezVous,
        ]);
    }

    // Sinon rediriger normalement
    return redirect()->back()->with('success', 'Votre rendez-vous a été enregistré avec succès.');
}

public function index()
{
    $services = Service::all(); // ou Service::orderBy('nom')->get();
    return view('dashboard', compact('services'));
}

 // Renvoie la liste JSON des rendez-vous de l'utilisateur
public function mesRendezVous()
{
    $userId = Auth::id();

    $rdvs = RendezVous::where('user_id', $userId)
        ->with('service')
        ->orderBy('date_heure', 'asc')
        ->get()
        ->map(function ($rdv) {
            // Convertir la string en Carbon
            $dateTime = Carbon::parse($rdv->date_heure);

            return [
                'id' => $rdv->id,
                'date' => $dateTime->format('d/m/Y'),
                'time' => $dateTime->format('H:i'),
                'service_name' => $rdv->service->service_name ?? '—',
            ];
        });

    return response()->json($rdvs);
}
    // Supprime un rendez-vous (annulation)
    public function annuler($id)
    {
        $userId = Auth::id();

        $rdv = RendezVous::where('id', $id)->where('user_id', $userId)->firstOrFail();

        $rdv->delete();

        return response()->json(['message' => 'Rendez-vous annulé avec succès']);
    }
}
