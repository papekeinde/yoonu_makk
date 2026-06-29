<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\StatutDemandeAdhesion;
use App\Http\Controllers\Controller;
use App\Mail\CompteGynecologueCreeMail;
use App\Models\DemandeAdhesion;
use App\Models\Gynecologue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DemandeAdhesionController extends Controller
{
    public function index(Request $request): View
    {
        $demandes = DemandeAdhesion::query()
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.demandes.index', compact('demandes'));
    }

    public function show(int $id): View
    {
        $demande = DemandeAdhesion::with('traitePar')->findOrFail($id);

        return view('admin.demandes.show', compact('demande'));
    }

    public function approuver(Request $request, int $id): RedirectResponse
    {
        $demande = DemandeAdhesion::findOrFail($id);

        if (! $demande->estEnAttente()) {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $demande->update([
            'statut'     => StatutDemandeAdhesion::Approuvee,
            'note_admin' => $request->note_admin,
            'traite_par' => $request->user()->id,
            'traite_le'  => now(),
        ]);

        $motDePasse = Str::random(12);

        $gynecologue = Gynecologue::create([
            'demande_adhesion_id' => $demande->id,
            'nom'                 => $demande->nom,
            'prenom'              => $demande->prenom,
            'email'               => $demande->email,
            'password'            => Hash::make($motDePasse),
            'telephone'           => $demande->telephone,
            'numero_ordre'        => $demande->numero_ordre,
            'specialite'          => $demande->specialite,
            'annees_experience'   => $demande->annees_experience,
            'structure_sante'     => $demande->structure_sante,
            'ville'               => $demande->ville,
        ]);

        Mail::to($gynecologue->email)->send(new CompteGynecologueCreeMail($gynecologue, $motDePasse));

        return redirect()->route('admin.demandes.index')
            ->with('success', 'Demande approuvée. Compte gynécologue créé et identifiants envoyés par email.');
    }

    public function rejeter(Request $request, int $id): RedirectResponse
    {
        $demande = DemandeAdhesion::findOrFail($id);

        if (! $demande->estEnAttente()) {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $demande->update([
            'statut'     => StatutDemandeAdhesion::Rejetee,
            'note_admin' => $request->note_admin,
            'traite_par' => $request->user()->id,
            'traite_le'  => now(),
        ]);

        return redirect()->route('admin.demandes.index')->with('success', 'Demande rejetée.');
    }
}
