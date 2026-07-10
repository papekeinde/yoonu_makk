<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garde l'accès aux pages d'administration web.
 * Contrairement à EnsureRole (API, réponses JSON), ce middleware redirige
 * vers la page de connexion ou renvoie un 403 lisible côté navigateur.
 */
class EnsureAdminWeb
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! Auth::user()->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Accès réservé aux administrateurs.']);
        }

        return $next($request);
    }
}
