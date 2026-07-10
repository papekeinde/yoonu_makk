<?php

namespace App\Http\Controllers\Api\Patient;

use App\Enums\StatutRendezVous;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreRendezVousRequest;
use App\Http\Resources\RendezVousResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rendezVous = $request->user()->femme
            ->rendezVous()
            ->with('gynecologue')
            ->orderByDesc('date_souhaitee')
            ->paginate(15);

        return response()->json(RendezVousResource::collection($rendezVous)->response()->getData(true));
    }

    public function store(StoreRendezVousRequest $request): JsonResponse
    {
        $rendezVous = $request->user()->femme->rendezVous()->create(
            $request->validated()
        );

        $rendezVous->load('gynecologue');

        return response()->json([
            'message'     => 'Demande de rendez-vous envoyée.',
            'rendez_vous' => new RendezVousResource($rendezVous),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $rendezVous = $request->user()->femme
            ->rendezVous()
            ->with('gynecologue')
            ->findOrFail($id);

        return response()->json(new RendezVousResource($rendezVous));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $rendezVous = $request->user()->femme->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::EnAttente) {
            return response()->json(['message' => 'Seuls les rendez-vous en attente peuvent être annulés.'], 422);
        }

        $rendezVous->update([
            'statut'           => StatutRendezVous::Annule,
            'annule_par'       => 'patiente',
            'raison_annulation' => 'Annulé par la patiente.',
        ]);

        return response()->json(['message' => 'Rendez-vous annulé.']);
    }
}
