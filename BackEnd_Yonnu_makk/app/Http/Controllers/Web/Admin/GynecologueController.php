<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gynecologue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GynecologueController extends Controller
{
    public function index(Request $request): View
    {
        $gynecologues = Gynecologue::query()
            ->when($request->ville, fn ($q, $v) => $q->where('ville', $v))
            ->when($request->statut === 'actif', fn ($q) => $q->where('is_active', true))
            ->when($request->statut === 'inactif', fn ($q) => $q->where('is_active', false))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gynecologues.index', compact('gynecologues'));
    }

    public function show(int $id): View
    {
        $gynecologue = Gynecologue::withCount('rendezVous')->findOrFail($id);

        return view('admin.gynecologues.show', compact('gynecologue'));
    }

    public function destroy(int $id): RedirectResponse
    {
        Gynecologue::findOrFail($id)->delete();

        return redirect()->route('admin.gynecologues.index')
            ->with('success', 'Gynécologue supprimé.');
    }

    public function activer(int $id): RedirectResponse
    {
        Gynecologue::findOrFail($id)->update(['is_active' => true]);

        return back()->with('success', 'Gynécologue activé.');
    }

    public function desactiver(int $id): RedirectResponse
    {
        Gynecologue::findOrFail($id)->update(['is_active' => false]);

        return back()->with('success', 'Gynécologue désactivé.');
    }
}
