<?php

namespace App\Http\Controllers\Web\Gynecologue;

use App\Http\Controllers\Controller;
use App\Models\Femme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Liste et dossier des patientes suivies par le gynécologue connecté.
 * Une « patiente » est une femme ayant au moins un rendez-vous avec lui.
 */
class PatienteController extends Controller
{
    public function index(Request $request): View
    {
        $gynecologue = Auth::guard('gynecologue_web')->user();

        // Identifiants des femmes ayant un rendez-vous avec ce gynécologue.
        $femmeIds = $gynecologue->rendezVous()->pluck('femme_id')->unique();

        $patientes = Femme::query()
            ->whereIn('id', $femmeIds)
            ->with(['user', 'grossesseActive'])
            ->withCount([
                'rendezVous as rdv_count' => fn ($q) => $q->where('gynecologue_id', $gynecologue->id),
            ])
            ->when($request->q, function ($query, $q) {
                $query->whereHas('user', function ($u) use ($q) {
                    $u->where('nom', 'like', "%{$q}%")
                      ->orWhere('prenom', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($request->profil, fn ($query, $p) => $query->where('type_profil', $p))
            ->get()
            ->sortBy(fn ($f) => $f->user?->prenom)
            ->values();

        return view('pro.patientes.index', compact('patientes'));
    }

    public function show(int $id): View
    {
        $gynecologue = Auth::guard('gynecologue_web')->user();

        // Accès restreint : la femme doit avoir un rendez-vous avec ce gynécologue.
        abort_unless(
            $gynecologue->rendezVous()->where('femme_id', $id)->exists(),
            404
        );

        $femme = Femme::with('user')->findOrFail($id);

        $grossesse = $femme->grossesseActive;

        $symptomes = $femme->symptomes()
            ->with('entrees')
            ->orderByDesc('date_journal')
            ->take(10)
            ->get();

        $rendezVous = $gynecologue->rendezVous()
            ->where('femme_id', $id)
            ->orderByDesc('date_souhaitee')
            ->get();

        $recommandations = $femme->recommandations()
            ->where('gynecologue_id', $gynecologue->id)
            ->latest()
            ->take(10)
            ->get();

        return view('pro.patientes.show', compact('femme', 'grossesse', 'symptomes', 'rendezVous', 'recommandations'));
    }
}
