<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreSuiviGrossesseRequest;
use App\Http\Resources\SuiviGrossesseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SuiviGrossesseController extends Controller
{
    public function index(): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $suivis = $grossesse->suivis()->orderByDesc('date_saisie')->get();

        return response()->json(SuiviGrossesseResource::collection($suivis)->response()->getData(true));
    }

    public function store(StoreSuiviGrossesseRequest $request): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $suivi = $grossesse->suivis()->create($request->validated());

        return response()->json([
            'message' => 'Suivi enregistré.',
            'suivi'   => new SuiviGrossesseResource($suivi),
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $suivi = $grossesse->suivis()->findOrFail($id);
        $suivi->delete();

        return response()->json(['message' => 'Suivi supprimé.']);
    }

    private function getGrossesseActive()
    {
        return Auth::user()->femme?->grossesseActive;
    }
}
