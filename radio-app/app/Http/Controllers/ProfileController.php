<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    /**
 * @OA\Get(
 *     path="/profile",
 *     summary="Afficher les informations du profil de l'utilisateur",
 *     description="Retourne les données du profil de l'utilisateur authentifié.",
 *     tags={"Profil"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Formulaire du profil affiché avec succès"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    /**
 * @OA\Patch(
 *     path="/profile",
 *     summary="Mettre à jour les informations du profil",
 *     description="Met à jour les informations du profil de l'utilisateur authentifié. Si l'email change, la vérification est réinitialisée.",
 *     tags={"Profil"},
 *     security={{"sessionAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Ayoub Berhili"),
 *             @OA\Property(property="email", type="string", format="email", example="ayoub@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Profil mis à jour et redirigé vers la page de profil"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    /**
 * @OA\Delete(
 *     path="/profile",
 *     summary="Supprimer le compte utilisateur",
 *     description="Supprime définitivement le compte de l'utilisateur après vérification du mot de passe.",
 *     tags={"Profil"},
 *     security={{"sessionAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"password"},
 *             @OA\Property(property="password", type="string", format="password", example="motdepasse123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Utilisateur déconnecté et redirigé vers la page d'accueil"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Mot de passe incorrect ou validation échouée"
 *     )
 * )
 */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
