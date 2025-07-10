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
     /**
     * @OA\Post(
     *     path="/rendez-vous-guest",
     *     summary="Créer un rendez-vous pour un visiteur non authentifié",
     *     tags={"Visiteur"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom_visiteur","email_visiteur","service_id","date_heure"},
     *             @OA\Property(property="nom_visiteur", type="string", example="Jean Dupont"),
     *             @OA\Property(property="telephone_visiteur", type="string", example="0612345678"),
     *             @OA\Property(property="email_visiteur", type="string", format="email", example="jean.dupont@mail.com"),
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="date_heure", type="string", format="date-time", example="2025-07-10T15:00:00"),
     *             @OA\Property(property="commentaire", type="string", example="Besoin d'un rendez-vous rapide."),
     *             @OA\Property(property="is_urgent", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rendez-vous enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rendez-vous enregistré avec succès."),
     *             @OA\Property(property="rendezVous", type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="visiteur_id", type="integer", example=45),
     *                 @OA\Property(property="service_id", type="integer", example=1),
     *                 @OA\Property(property="date_heure", type="string", format="date-time", example="2025-07-10T15:00:00"),
     *                 @OA\Property(property="commentaire", type="string", example="Besoin d'un rendez-vous rapide."),
     *                 @OA\Property(property="is_urgent", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation échouée")
     * )
     */
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

 /**
     * @OA\Get(
     *     path="/guest-mes-rendezvous",
     *     summary="Lister les rendez-vous d'un visiteur par email",
     *     tags={"Visiteur"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email du visiteur",
     *         required=true,
     *         @OA\Schema(type="string", format="email", example="jean.dupont@mail.com")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des rendez-vous",
     *         @OA\JsonContent(type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="date", type="string", example="10/07/2025"),
     *                 @OA\Property(property="time", type="string", example="15:00"),
     *                 @OA\Property(property="service_name", type="string", example="Radiologie")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation échouée"),
     *     @OA\Response(response=404, description="Visiteur non trouvé")
     * )
     */
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
