<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
 * @OA\Get(
 *     path="/admin/dashboard",
 *     summary="Afficher les rendez-vous urgents pour l'administrateur",
 *     description="Retourne une liste de tous les rendez-vous urgents avec les utilisateurs et visiteurs associés. Accessible uniquement aux administrateurs connectés.",
 *     tags={"Admin"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des rendez-vous urgents",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="date_heure", type="string", format="date-time"),
 *                 @OA\Property(property="is_urgent", type="boolean"),
 *                 @OA\Property(property="user", type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="email", type="string")
 *                 ),
 *                 @OA\Property(property="visiteur", type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="nom_visiteur", type="string"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="telephone", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers la page de connexion si l'utilisateur n'est pas admin ou non connecté"
 *     )
 * )
 */


    public function index()
    {
        // Vérification si l'utilisateur est authentifié et a le rôle d'administrateur
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect('/login')->with('error', 'Accès non autorisé');
        }
        else{
            $rdvUrgents = RendezVous::with(['user', 'visiteur'])
                ->where('is_urgent', true)
                ->get();
        }
        // Logique pour afficher le tableau de bord de l'administrateur
        return view('admin.dashboard',compact('rdvUrgents'));
    }



}
