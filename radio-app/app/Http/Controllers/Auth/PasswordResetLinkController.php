<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    /**
 * @OA\Get(
 *     path="/forgot-password",
 *     summary="Afficher le formulaire de demande de lien de réinitialisation du mot de passe",
 *     description="Affiche la page permettant à l'utilisateur de saisir son email pour recevoir un lien de réinitialisation.",
 *     tags={"Mot de passe"},
 *     @OA\Response(
 *         response=200,
 *         description="Formulaire affiché"
 *     )
 * )
 */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

/**
 * @OA\Post(
 *     path="/forgot-password",
 *     summary="Envoyer un lien de réinitialisation du mot de passe",
 *     description="Valide l'email et envoie un lien de réinitialisation si l'adresse est valide.",
 *     tags={"Mot de passe"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Retour arrière avec message de succès ou d'erreur"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation (ex: email invalide)"
 *     )
 * )
 */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
