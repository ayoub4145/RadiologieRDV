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

        // Skip middleware for 2FA-related routes to avoid circular redirects
        if ($request->routeIs('2fa.*') || $request->is('2fa/*')) {
            return $next($request);
        }

        if ($user) {
            // Si l'utilisateur n'a pas de clé secrète, le rediriger vers setup
            if (!$user->google2fa_secret) {
                return redirect('/2fa/setup');
            }

            // Si la clé existe mais 2FA n'est pas activé, rediriger vers setup
            if (!$user->two_factor_enabled) {
                return redirect('/2fa/setup');
            }

            // Si 2FA est activé mais pas validé pour cette session, rediriger vers verify
            if (!session('2fa_passed')) {
                return redirect('/2fa/verify');
            }
        }

        return $next($request);
    }
}
