<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Connexion / déconnexion de l'espace web.
 *
 * Point d'entrée unique : à partir des seuls identifiants, on devine l'espace
 * (administration ou gynécologue) et on dirige l'utilisateur vers son tableau
 * de bord. Les deux comptes vivent dans des tables distinctes, donc aucun
 * risque de collision d'email entre les deux guards.
 */
class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('gynecologue_web')->check()) {
            return redirect()->route('pro.dashboard');
        }

        return view('auth.login');
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

        $remember = $request->boolean('remember');

        // 1) Espace administration (table users).
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            if (! Auth::guard('web')->user()->isAdmin()) {
                Auth::guard('web')->logout();

                return back()
                    ->withErrors(['email' => 'Cet espace est réservé au personnel autorisé.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // 2) Espace gynécologue (table gynecologues).
        if (Auth::guard('gynecologue_web')->attempt($credentials, $remember)) {
            if (! Auth::guard('gynecologue_web')->user()->is_active) {
                Auth::guard('gynecologue_web')->logout();

                return back()
                    ->withErrors(['email' => 'Votre compte est désactivé. Contactez l\'administration.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('pro.dashboard'));
        }

        // 3) Aucun des deux espaces ne reconnaît ces identifiants.
        return back()
            ->withErrors(['email' => 'Identifiants incorrects.'])
            ->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
