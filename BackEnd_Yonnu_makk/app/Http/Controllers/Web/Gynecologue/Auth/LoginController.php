<?php

namespace App\Http\Controllers\Web\Gynecologue\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Connexion / déconnexion de l'espace web gynécologue (guard "gynecologue_web", session).
 */
class LoginController extends Controller
{
    public function show(): RedirectResponse
    {
        if (Auth::guard('gynecologue_web')->check()) {
            return redirect()->route('pro.dashboard');
        }

        // Point d'entrée unique : on renvoie vers la connexion commune,
        // qui devine l'espace à partir des identifiants.
        return redirect()->route('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email'    => 'adresse email',
            'password' => 'mot de passe',
        ]);

        if (! Auth::guard('gynecologue_web')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        if (! Auth::guard('gynecologue_web')->user()->is_active) {
            Auth::guard('gynecologue_web')->logout();

            return back()
                ->withErrors(['email' => 'Votre compte est désactivé. Contactez l\'administration.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('pro.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('gynecologue_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pro.login');
    }
}
