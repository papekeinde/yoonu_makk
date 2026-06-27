<?php

namespace App\Http\Controllers\Api\Patient;

use App\Enums\TypeProfilFemme;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreGrossesseRequest;
use App\Http\Resources\GrossesseResource;
use App\Models\Grossesse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GrossesseController extends Controller
{
    public function show(): JsonResponse
    {
        $femme = Auth::user()->femme;

        $grossesse = $femme->grossesseActive;

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        return response()->json(new GrossesseResource($grossesse));
    }

    public function store(StoreGrossesseRequest $request): JsonResponse
    {
        $femme = Auth::user()->femme;

        // Désactiver les grossesses précédentes
        $femme->grossesses()->where('grossesse_active', true)->update(['grossesse_active' => false]);

        $grossesse = $femme->grossesses()->create($request->validated());

        // Mettre à jour le profil de la femme
        $femme->update(['type_profil' => TypeProfilFemme::Grossesse->value]);

        return response()->json([
            'message'   => 'Grossesse enregistrée avec succès.',
            'grossesse' => new GrossesseResource($grossesse),
        ], 201);
    }

    public function terminer(): JsonResponse
    {
        $femme = Auth::user()->femme;

        $grossesse = $femme->grossesseActive;

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $grossesse->update(['grossesse_active' => false]);
        $femme->update(['type_profil' => TypeProfilFemme::Menopause->value]);

        return response()->json(['message' => 'Grossesse clôturée.']);
    }
}
