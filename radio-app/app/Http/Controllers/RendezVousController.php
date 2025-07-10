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
/**
 * @OA\Post(
 *     path="/prendre-rdv",
 *     summary="Prendre un nouveau rendez-vous",
 *     description="Crée un rendez-vous pour l'utilisateur authentifié avec un service, une date, etc.",
 *     tags={"RendezVous"},
 *     security={{"sessionAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"service_id", "date_heure"},
 *             @OA\Property(property="service_id", type="integer", example=1),
 *             @OA\Property(property="date_heure", type="string", format="date-time", example="2025-07-15T14:00:00"),
 *             @OA\Property(property="commentaire", type="string", example="Préférence pour le matin"),
 *             @OA\Property(property="is_urgent", type="boolean", example=false),
 *             @OA\Property(property="nom", type="string", example="Jean Dupont"),
 *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Rendez-vous enregistré avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="rendezVous", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="service_id", type="integer"),
 *                 @OA\Property(property="date_heure", type="string", format="date-time"),
 *                 @OA\Property(property="is_urgent", type="boolean"),
 *                 @OA\Property(property="commentaire", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Données invalides"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'service_id'   => 'required|exists:services,id',
        'date_heure'   => 'required|date|after:now',
        'commentaire'  => 'nullable|string',
        // 'visiteur_id'  => 'nullable|exists:visiteur,id',
    ]);
    // Vérifie si un visiteur avec cet email existe déjà
  
    $rendezVous = new RendezVous();
    $rendezVous->user_id     = Auth::id();
    $rendezVous->service_id  = $validated['service_id'];
    $rendezVous->date_heure  = $validated['date_heure'];
    $rendezVous->is_urgent   = $request->has('is_urgent') ? 1 : 0;
    $rendezVous->commentaire = $validated['commentaire'] ?? null;
    // $rendezVous->statut      = $rendezVous->is_urgent ? 'en_attente' : 'confirmé';
    // $rendezVous->visiteur_id = $validated['visiteur_id'] ?? $visiteur->id;
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
/**
 * @OA\Get(
 *     path="/dashboard",
 *     summary="Afficher la page dashboard avec la liste des services",
 *     tags={"RendezVous"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Page dashboard retournée avec succès"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
public function index()
{
    $services = Service::all(); // ou Service::orderBy('nom')->get();
    return view('dashboard', compact('services'));
}
/**
 * @OA\Get(
 *     path="/mes-rendezvous",
 *     summary="Récupérer la liste des rendez-vous de l'utilisateur",
 *     tags={"RendezVous"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste JSON des rendez-vous",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="date", type="string", example="15/07/2025"),
 *                 @OA\Property(property="time", type="string", example="14:00"),
 *                 @OA\Property(property="service_name", type="string", example="Consultation générale")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
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
/**
 * @OA\Delete(
 *     path="/annuler-rendezvous/{id}",
 *     summary="Annuler un rendez-vous",
 *     description="Supprime un rendez-vous appartenant à l'utilisateur authentifié.",
 *     tags={"RendezVous"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du rendez-vous à annuler",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Rendez-vous annulé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Rendez-vous annulé avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Rendez-vous non trouvé"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    // Supprime un rendez-vous (annulation)
    public function annuler($id)
    {
        $userId = Auth::id();

        $rdv = RendezVous::where('id', $id)->where('user_id', $userId)->firstOrFail();

        $rdv->delete();

        return response()->json(['message' => 'Rendez-vous annulé avec succès']);
    }
}
