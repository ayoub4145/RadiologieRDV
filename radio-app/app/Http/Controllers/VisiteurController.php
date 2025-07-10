<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use App\Models\Visiteur;

class VisiteurController extends Controller
{
    /**
 * @OA\Get(
 *     path="/rendez-vous-guest",
 *     summary="Afficher le formulaire de prise de rendez-vous pour visiteurs",
 *     description="Retourne la vue du formulaire pour les visiteurs non authentifiés.",
 *     tags={"Visiteur"},
 *     @OA\Response(
 *         response=200,
 *         description="Formulaire affiché avec succès"
 *     )
 * )
 */
    public function index()
    {
        $services = \App\Models\Service::all();

        return view('rendez-vous-guest',compact('services'));
    }
public function store(Request $request)
{
    // Validation des données
    $validatedData = $request->validate([
        'nom_visiteur' => 'required|string|max:255',
        'telephone_visiteur' => 'nullable|string|max:20',
        'email_visiteur' => 'required|email',
        'service_id' => 'required|exists:services,id',
        'date_heure' => 'required|date|after:now',
        'commentaire' => 'nullable|string',
        'is_urgent' => 'nullable|boolean',
    ]);

    // Création ou récupération du visiteur via email
    $visiteur = Visiteur::firstOrCreate(
        ['email' => $validatedData['email_visiteur']],
        [
            'name' => $validatedData['nom_visiteur'],
            'telephone' => $validatedData['telephone_visiteur'] ?? null,
        ]
    );

    // Création du rendez-vous
    $rdv = new RendezVous();
    $rdv->user_id = null; // utilisateur non connecté
    $rdv->visiteur_id = $visiteur->id;
    $rdv->service_id = $validatedData['service_id']??null;
    $rdv->date_heure = $validatedData['date_heure'];
    $rdv->commentaire = $validatedData['commentaire'] ?? null;
    $rdv->is_urgent = $validatedData['is_urgent'] ?? false;
    $rdv->save();

    return response()->json([
        'message' => 'Rendez-vous enregistré avec succès.',
        'rendezVous' => $rdv,
    ]);
}


    public function mesRendezVous(Request $request)
    {
       $request->validate([
        'email' => 'required|email|exists:visiteur,email',
    ]);

    $visiteur = Visiteur::where('email', $request->email)->first();

    $rdvs = RendezVous::whereNull('user_id')
        ->where('visiteur_id', $visiteur->id)
        ->with('service')
        ->orderBy('date_heure', 'desc')
        ->get()
        ->map(function ($rdv) {
            $dt = \Carbon\Carbon::parse($rdv->date_heure);
            return [
                'id' => $rdv->id,
                'date' => $dt->format('d/m/Y'),
                'time' => $dt->format('H:i'),
                'service_name' => $rdv->service?->service_name ?? '—',
            ];
        });

    return response()->json($rdvs);
    }

}
