<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/**
 * @OA\Get(
 *     path="/medecin/dashboard",
 *     summary="Afficher le tableau de bord du médecin",
 *     description="Retourne la vue du tableau de bord pour un médecin authentifié.",
 *     tags={"Médecin"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Vue du tableau de bord affichée avec succès."
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non authentifié."
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé. L'utilisateur n'est pas médecin."
 *     )
 * )
 */
class MedecinController extends Controller
{
    public function index()
    {
        // Logique pour afficher le tableau de bord du médecin
        return view('medecin.dashboard');
    }
}
