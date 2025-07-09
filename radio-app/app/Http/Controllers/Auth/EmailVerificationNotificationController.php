<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    /**
 * @OA\Post(
 *     path="/email/verification-notification",
 *     summary="Envoyer une nouvelle notification de vérification d'email",
 *     description="Envoie un email de vérification si l'email de l'utilisateur n'est pas encore vérifié.",
 *     tags={"Email Verification"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers le dashboard si l'email est déjà vérifié"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Retour arrière avec statut d'envoi du lien de vérification"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non authentifié"
 *     )
 * )
 */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
