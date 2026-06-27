<?php

namespace App\Http\Controllers\Api\Gynecologue;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gynecologue\StoreRecommandationRequest;
use App\Http\Resources\GrossesseResource;
use App\Http\Resources\RecommandationResource;
use App\Http\Resources\SuiviGrossesseResource;
use App\Http\Resources\SymptomeResource;
use App\Models\Femme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatienteController extends Controller
{
    public function symptomes(Request $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        $aRendezVous = $gynecologue->rendezVous()->where('femme_id', $femmeId)->exists();

        if (! $aRendezVous) {
            return response()->json(['message' => 'Vous n\'avez aucun rendez-vous avec cette patiente.'], 403);
        }

        $femme = Femme::findOrFail($femmeId);

        $symptomes = $femme->symptomes()
            ->with('entrees')
            ->orderByDesc('date_journal')
            ->paginate(15);

        return response()->json(SymptomeResource::collection($symptomes)->response()->getData(true));
    }

    public function recommander(StoreRecommandationRequest $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        $aRendezVous = $gynecologue->rendezVous()->where('femme_id', $femmeId)->exists();

        if (! $aRendezVous) {
            return response()->json(['message' => 'Vous n\'avez aucun rendez-vous avec cette patiente.'], 403);
        }

        $femme = Femme::findOrFail($femmeId);

        $recommandation = $femme->recommandations()->create([
            ...$request->validated(),
            'genere_par'     => 'gynecologue',
            'gynecologue_id' => $gynecologue->id,
        ]);

        return response()->json([
            'message'         => 'Recommandation envoyée.',
            'recommandation'  => new RecommandationResource($recommandation),
        ], 201);
    }

    public function grossesse(Request $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        $aRendezVous = $gynecologue->rendezVous()->where('femme_id', $femmeId)->exists();

        if (! $aRendezVous) {
            return response()->json(['message' => 'Vous n\'avez aucun rendez-vous avec cette patiente.'], 403);
        }

        $femme = Femme::findOrFail($femmeId);
        $grossesse = $femme->grossesseActive;

        if (! $grossesse) {
            return response()->json(['message' => 'Cette patiente n\'a pas de grossesse active.'], 404);
        }

        return response()->json([
            'grossesse' => new GrossesseResource($grossesse),
            'suivis'    => SuiviGrossesseResource::collection($grossesse->suivis()->orderByDesc('date_saisie')->get()),
        ]);
    }
}
