<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('rendez-vous-guest');
    }
    public function store(Request $request)
    {

    }

}
