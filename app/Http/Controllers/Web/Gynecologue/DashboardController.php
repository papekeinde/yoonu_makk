<?php

namespace App\Http\Controllers\Web\Gynecologue;

use App\Enums\StatutRendezVous;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $gynecologue = Auth::guard('gynecologue_web')->user();

        $stats = [
            'en_attente' => $gynecologue->rendezVous()->where('statut', StatutRendezVous::EnAttente->value)->count(),
            'accepte'    => $gynecologue->rendezVous()->where('statut', StatutRendezVous::Accepte->value)->count(),
            'termine'    => $gynecologue->rendezVous()->where('statut', StatutRendezVous::Termine->value)->count(),
            'patientes'  => $gynecologue->rendezVous()->distinct('femme_id')->count('femme_id'),
        ];

        $prochains = $gynecologue->rendezVous()
            ->with('femme.user')
            ->where('statut', StatutRendezVous::Accepte->value)
            ->orderBy('date_confirmee')
            ->take(5)
            ->get();

        $aTraiter = $gynecologue->rendezVous()
            ->with('femme.user')
            ->where('statut', StatutRendezVous::EnAttente->value)
            ->orderByDesc('date_souhaitee')
            ->take(5)
            ->get();

        return view('pro.dashboard', compact('gynecologue', 'stats', 'prochains', 'aTraiter'));
    }
}
