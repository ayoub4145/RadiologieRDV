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
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
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
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
