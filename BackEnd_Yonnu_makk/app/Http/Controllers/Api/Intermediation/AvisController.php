<?php

namespace App\Http\Controllers\Api\Intermediation;

use App\Http\Controllers\Controller;
use App\Models\AvisProfessionnel;
use App\Models\DemandeIntermediation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    /** POST /api/intermediation/avis — laisser un avis après consultation */
    public function store(Request $request): JsonResponse
    {
        $femme = Auth::user()->femme;

        $validated = $request->validate([
            'professionnel_id' => 'required|exists:professionnels_sante,id',
            'demande_id'       => 'nullable|exists:demandes_intermediation,id',
            'note'             => 'required|integer|min:1|max:5',
            'commentaire'      => 'nullable|string|max:500',
            'recommande'       => 'boolean',
        ]);

        // Vérifier que la demande appartient à la femme et est terminée
        if (!empty($validated['demande_id'])) {
            $demande = DemandeIntermediation::findOrFail($validated['demande_id']);
            abort_if($demande->femme_id !== $femme->id, 403);
            abort_if($demande->statut !== 'termine', 422, 'Vous ne pouvez noter qu\'une consultation terminée.');
        }

        $avis = AvisProfessionnel::updateOrCreate(
            ['femme_id' => $femme->id, 'professionnel_id' => $validated['professionnel_id']],
            $validated
        );

        return response()->json(['message' => 'Avis enregistré. Il sera publié après modération.', 'avis' => $avis], 201);
    }
}
