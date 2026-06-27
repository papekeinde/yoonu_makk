<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDemandeAdhesionRequest;
use App\Http\Resources\DemandeAdhesionResource;
use App\Models\DemandeAdhesion;
use Illuminate\Http\JsonResponse;

class DemandeAdhesionPublicController extends Controller
{
    public function store(StoreDemandeAdhesionRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('diplome')) {
            $data['chemin_diplome'] = $request->file('diplome')->store('demandes/diplomes', 'public');
        }
        if ($request->hasFile('justificatif')) {
            $data['chemin_justificatif'] = $request->file('justificatif')->store('demandes/justificatifs', 'public');
        }

        unset($data['diplome'], $data['justificatif']);

        $demande = DemandeAdhesion::create($data);

        return response()->json([
            'message' => 'Votre demande d\'adhésion a été envoyée. Vous serez contacté après validation.',
            'demande' => new DemandeAdhesionResource($demande),
        ], 201);
    }
}
