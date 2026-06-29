<?php

namespace App\Http\Controllers\Web\Gynecologue;

use App\Http\Controllers\Controller;
use App\Models\Gynecologue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(): View
    {
        $gynecologue = Auth::guard('gynecologue_web')->user();

        return view('pro.profil.edit', compact('gynecologue'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Gynecologue $gynecologue */
        $gynecologue = Auth::guard('gynecologue_web')->user();

        $data = $request->validate([
            'prenom'             => ['required', 'string', 'max:100'],
            'nom'                => ['required', 'string', 'max:100'],
            'email'              => ['required', 'email', 'max:255', Rule::unique('gynecologues', 'email')->ignore($gynecologue->id)],
            'telephone'          => ['nullable', 'string', 'max:30'],
            'specialite'         => ['nullable', 'string', 'max:150'],
            'annees_experience'  => ['nullable', 'integer', 'min:0', 'max:80'],
            'structure_sante'    => ['nullable', 'string', 'max:200'],
            'ville'              => ['nullable', 'string', 'max:100'],
            'tarif_consultation' => ['nullable', 'integer', 'min:0'],
            'bio'                => ['nullable', 'string', 'max:2000'],
        ]);

        $gynecologue->update($data);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function changerMotDePasse(Request $request): RedirectResponse
    {
        /** @var Gynecologue $gynecologue */
        $gynecologue = Auth::guard('gynecologue_web')->user();

        $data = $request->validate([
            'mot_de_passe_actuel'   => ['required', 'string'],
            'nouveau_mot_de_passe'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'mot_de_passe_actuel'  => 'mot de passe actuel',
            'nouveau_mot_de_passe' => 'nouveau mot de passe',
        ]);

        if (! Hash::check($data['mot_de_passe_actuel'], $gynecologue->password)) {
            return back()->withErrors(['mot_de_passe_actuel' => 'Le mot de passe actuel est incorrect.']);
        }

        $gynecologue->update(['password' => $data['nouveau_mot_de_passe']]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
