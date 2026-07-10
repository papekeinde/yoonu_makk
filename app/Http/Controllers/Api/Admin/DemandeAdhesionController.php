<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatutDemandeAdhesion;
use App\Http\Controllers\Controller;
use App\Http\Resources\DemandeAdhesionResource;
use App\Mail\CompteGynecologueCreeMail;
use App\Models\DemandeAdhesion;
use App\Models\Gynecologue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DemandeAdhesionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $demandes = DemandeAdhesion::query()
            ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(DemandeAdhesionResource::collection($demandes)->response()->getData(true));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(new DemandeAdhesionResource(DemandeAdhesion::findOrFail($id)));
    }

    public function approuver(Request $request, int $id): JsonResponse
    {
        $demande = DemandeAdhesion::findOrFail($id);

        if (! $demande->estEnAttente()) {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 422);
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

        return response()->json([
            'message' => 'Demande approuvée. Compte gynécologue créé et identifiants envoyés par email.',
        ]);
    }

    public function rejeter(Request $request, int $id): JsonResponse
    {
        $demande = DemandeAdhesion::findOrFail($id);

        if (! $demande->estEnAttente()) {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $demande->update([
            'statut'     => StatutDemandeAdhesion::Rejetee,
            'note_admin' => $request->note_admin,
            'traite_par' => $request->user()->id,
            'traite_le'  => now(),
        ]);

        return response()->json(['message' => 'Demande rejetée.']);
    }
}
