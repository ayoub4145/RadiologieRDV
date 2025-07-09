<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    /**
 * @OA\Get(
 *     path="/email/verify",
 *     summary="Afficher la page de demande de vérification d'email",
 *     description="Affiche la page demandant à l'utilisateur de vérifier son email, ou redirige vers le dashboard si déjà vérifié.",
 *     tags={"Email Verification"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Page de vérification affichée"
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers le dashboard si email déjà vérifié"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non authentifié"
 *     )
 * )
 */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
