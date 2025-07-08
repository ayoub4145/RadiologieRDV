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

        if ($user) {
            // Cas 1 : L’utilisateur n’a pas encore de clé secrète
            if (!$user->google2fa_secret) {
                return redirect()->route('2fa.setup');
            }

            // Cas 2 : Clé présente mais 2FA non activé
            if (!$user->two_factor_enabled) {
                return redirect()->route('2fa.setup');
            }

            // Cas 3 : 2FA activé mais pas encore validé pour cette session
            if (!session('2fa_passed')) {
                return redirect()->route('2fa.verify.form');
            }
        }

        return $next($request);
    }
}
