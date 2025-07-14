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
 *     summary="Dashboard Admin",
 *     tags={"Admin"},
 *     description="Retourne la vue dashboard pour les admins connectés.",
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Succès"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
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

    public function showUrgentRendezvous()
    {
        return view('admin.dashboard', compact('rdvUrgents'));
    }

}
