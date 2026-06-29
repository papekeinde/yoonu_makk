<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RendezVousController extends Controller
{
    public function index(Request $request): View
    {
        $rendezVous = RendezVous::with(['gynecologue', 'femme.user'])
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->orderByDesc('date_souhaitee')
            ->paginate(20)
            ->withQueryString();

        return view('admin.rendez-vous.index', compact('rendezVous'));
    }
}
