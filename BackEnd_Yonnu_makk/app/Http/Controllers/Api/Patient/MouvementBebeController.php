<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreMouvementBebeRequest;
use App\Http\Resources\MouvementBebeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MouvementBebeController extends Controller
{
    public function index(): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $mouvements = $grossesse->mouvements()->orderByDesc('date_heure')->paginate(20);

        return response()->json([
            'data'  => MouvementBebeResource::collection($mouvements->items()),
            'total' => $mouvements->total(),
            'pages' => $mouvements->lastPage(),
        ]);
    }

    public function store(StoreMouvementBebeRequest $request): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $mouvement = $grossesse->mouvements()->create($request->validated());

        return response()->json([
            'message'   => 'Mouvement enregistré.',
            'mouvement' => new MouvementBebeResource($mouvement),
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $mouvement = $grossesse->mouvements()->findOrFail($id);
        $mouvement->delete();

        return response()->json(['message' => 'Mouvement supprimé.']);
    }

    public function resumeJour(): JsonResponse
    {
        $grossesse = $this->getGrossesseActive();

        if (! $grossesse) {
            return response()->json(['message' => 'Aucune grossesse active.'], 404);
        }

        $today = today()->toDateString();

        $total = $grossesse->mouvements()
            ->whereDate('date_heure', $today)
            ->sum('nombre_mouvements');

        $total = (int) $total;

        return response()->json([
            'date'              => $today,
            'total_mouvements'  => $total,
            'objectif_journalier' => 10,
            'objectif_atteint'  => $total >= 10,
        ]);
    }

    private function getGrossesseActive()
    {
        return Auth::user()->femme?->grossesseActive;
    }
}
