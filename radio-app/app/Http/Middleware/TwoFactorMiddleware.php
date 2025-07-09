<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
           $user = Auth::user();

        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion.
        // Placer cette vérification au début simplifie la suite du code.
        if (!$user) {
            return redirect()->route('login');
        }

        // Bypass pour l'admin ou si la route est déjà une route 2FA (pour éviter les boucles)
        if ($user->role === 'admin' || $this->isTwoFactorRoute($request)) {
            return $next($request);
        }

        // Si la 2FA n'est pas configurée ou pas activée, rediriger vers la page de configuration.
        // On combine les deux conditions car elles mènent à la même action.
        if (!$user->google2fa_secret || !$user->two_factor_enabled) {
            return redirect('/2fa/setup');
        }

        // Si la 2FA n'a pas été validée dans la session actuelle, rediriger vers la page de vérification.
        if (!session('2fa_passed')) {
            return redirect('/2fa/verify');
        }

        // L'utilisateur a passé toutes les vérifications, on continue la requête.
        return $next($request);
    }
        /**
     * Vérifie si la requête concerne une route de la 2FA.
     */
    protected function isTwoFactorRoute(Request $request): bool
    {
        return $request->routeIs('2fa.*') || $request->is('2fa/*');
    }
}
