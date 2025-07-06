<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next,string $role): Response
    {
         if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (!$user->role || $user->role->name !== $role) {
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }
}
