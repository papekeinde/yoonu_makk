<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UtilisateurController extends Controller
{
    public function index(Request $request): View
    {
        $utilisateurs = User::query()
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

    public function show(int $id): View
    {
        $utilisateur = User::with('femme')->findOrFail($id);

        return view('admin.utilisateurs.show', compact('utilisateur'));
    }

    public function destroy(int $id): RedirectResponse
    {
        User::findOrFail($id)->delete();

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}
