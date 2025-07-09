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
    /**
 * @OA\Get(
 *     path="/creneaux/{service_id}",
 *     summary="Lister les créneaux disponibles pour un service donné",
 *     description="Retourne les créneaux disponibles, avec un traitement spécial si le paramètre urgent=1 est présent.",
 *     tags={"Créneaux"},
 *     @OA\Parameter(
 *         name="service_id",
 *         in="path",
 *         required=true,
 *         description="Identifiant du service",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="urgent",
 *         in="query",
 *         required=false,
 *         description="Si égal à 1, filtre les créneaux pour les 24 prochaines heures",
 *         @OA\Schema(type="integer", enum={0,1})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des créneaux disponibles",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="date", type="string", format="date"),
 *                 @OA\Property(property="time", type="string", example="09:00"),
 *                 @OA\Property(property="end_time", type="string", example="10:00"),
 *                 @OA\Property(property="service_id", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Service non trouvé"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur serveur"
 *     )
 * )
 */
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
/**
 * @OA\Get(
 *     path="/api/creneaux-disponibles/{serviceId}",
 *     summary="Lister les créneaux disponibles pour un service",
 *     description="Retourne les créneaux disponibles filtrés selon l'urgence (paramètre urgent=1).",
 *     tags={"Créneaux"},
 *     @OA\Parameter(
 *         name="serviceId",
 *         in="path",
 *         required=true,
 *         description="ID du service médical",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="urgent",
 *         in="query",
 *         required=false,
 *         description="Si 1, affiche uniquement les créneaux des prochaines 24h",
 *         @OA\Schema(type="integer", enum={0,1})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des créneaux disponibles",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="date", type="string", format="date"),
 *                 @OA\Property(property="time", type="string", example="09:00"),
 *                 @OA\Property(property="service_id", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur serveur"
 *     )
 * )
 */
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
