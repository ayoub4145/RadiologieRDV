<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // Logique pour afficher le tableau de bord de l'administrateur
        return view('admin.dashboard');
    }
}
