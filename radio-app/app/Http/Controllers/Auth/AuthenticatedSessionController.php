<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    /**
 * @OA\Get(
 *     path="/login",
 *     summary="Afficher la page de connexion",
 *     tags={"Authentification"},
 *     @OA\Response(
 *         response=200,
 *         description="Page de connexion affichée"
 *     )
 * )
 */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    /**
 * @OA\Post(
 *     path="/login",
 *     summary="Authentifier un utilisateur",
 *     description="Gère la soumission du formulaire de connexion, envoie un code 2FA, puis redirige selon le rôle.",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="remember", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Response(
 *         response=302,
 *         description="Redirection après authentification réussie"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Identifiants invalides"
 *     )
 * )
 */
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();  // Authentifie l'utilisateur
    $request->session()->regenerate();

    // Génére et envoie le code 2FA
    $user = $request->user();
    $user->generateTwoFactorCode(); // à définir dans User.php si pas encore fait
    $user->notify(new TwoFactorCodeNotification());

    // Supprime toute session 2fa passée
    session()->forget('2fa_passed');

    // Redirige vers la page de vérification 2FA
    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'medecin') {
        return redirect('/medecin/dashboard');
    }

    return redirect('/dashboard'); // patient
}
    /**
     * Destroy an authenticated session.
     */
    /**
 * @OA\Post(
 *     path="/logout",
 *     summary="Déconnecter l'utilisateur",
 *     description="Déconnecte l'utilisateur et invalide la session.",
 *     tags={"Authentification"},
 *     security={{"sessionAuth": {}}},
 *     @OA\Response(
 *         response=302,
 *         description="Redirection vers la page d'accueil après déconnexion"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
