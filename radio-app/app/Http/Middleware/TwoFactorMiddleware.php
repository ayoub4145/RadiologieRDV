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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
 if (
        Auth::check() &&
        Auth::user()->two_factor_code &&
        !session()->get('2fa_passed') &&
        !$request->is('2fa') &&
        !$request->is('2fa/*')&&
        !$request->is('logout')

    ) {
        return redirect()->route('2fa.verify');
    }

    return $next($request);
    }
}
