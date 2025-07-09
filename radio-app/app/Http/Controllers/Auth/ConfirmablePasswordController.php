<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    /**
 * @OA\Get(
 *     path="/confirm-password",
 *     summary="Afficher la page de confirmation du mot de passe",
 *     tags={"Authentification"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Page de confirmation affichée"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non authentifié"
 *     )
 * )
 */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    /**
 * @OA\Post(
 *     path="/confirm-password",
 *     summary="Confirmer le mot de passe de l'utilisateur",
 *     description="Valide le mot de passe actuel pour autoriser des actions sensibles.",
 *     tags={"Authentification"},
 *     security={{"sessionAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"password"},
 *             @OA\Property(property="password", type="string", format="password", example="current_password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers la page protégée (ex : dashboard) en cas de succès"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation échouée (mot de passe incorrect)"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non authentifié"
 *     )
 * )
 */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
