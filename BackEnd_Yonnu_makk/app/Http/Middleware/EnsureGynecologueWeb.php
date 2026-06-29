<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garde l'accès aux pages de l'espace web gynécologue (guard "gynecologue_web", session).
 * Redirige vers la connexion pro et refuse les comptes désactivés.
 */
class EnsureGynecologueWeb
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('gynecologue_web')->check()) {
            return redirect()->route('pro.login');
        }

        if (! Auth::guard('gynecologue_web')->user()->is_active) {
            Auth::guard('gynecologue_web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('pro.login')
                ->withErrors(['email' => 'Votre compte est désactivé. Contactez l\'administration.']);
        }

        return $next($request);
    }
}
