<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    /**
 * @OA\Post(
 *     path="/user/password",
 *     summary="Mettre à jour le mot de passe de l'utilisateur",
 *     description="Permet à l'utilisateur connecté de changer son mot de passe en validant l'ancien.",
 *     tags={"Mot de passe"},
 *     security={{"sessionAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"current_password","password","password_confirmation"},
 *             @OA\Property(property="current_password", type="string", format="password", example="oldPassword123"),
 *             @OA\Property(property="password", type="string", format="password", example="newStrongPassword456!"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newStrongPassword456!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Retour à la page précédente avec confirmation de mise à jour"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation (ex: mot de passe actuel incorrect, confirmation invalide)"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non authentifié"
 *     )
 * )
 */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
