<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    /**
 * @OA\Get(
 *     path="/reset-password",
 *     summary="Afficher le formulaire de réinitialisation du mot de passe",
 *     description="Affiche la page pour saisir un nouveau mot de passe via un token reçu par email.",
 *     tags={"Mot de passe"},
 *     @OA\Parameter(
 *         name="token",
 *         in="query",
 *         required=true,
 *         description="Token de réinitialisation du mot de passe",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Formulaire affiché"
 *     )
 * )
 */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    /**
 * @OA\Post(
 *     path="/reset-password",
 *     summary="Traiter la réinitialisation du mot de passe",
 *     description="Valide et met à jour le nouveau mot de passe de l'utilisateur.",
 *     tags={"Mot de passe"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"token", "email", "password", "password_confirmation"},
 *             @OA\Property(property="token", type="string", example="reset_token_123"),
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newStrongPassword123!"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newStrongPassword123!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers la page de connexion après succès"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation (ex: token invalide ou mot de passe non confirmé)"
 *     )
 * )
 */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
