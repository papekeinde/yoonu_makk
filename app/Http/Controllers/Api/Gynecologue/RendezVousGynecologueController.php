<?php

namespace App\Http\Controllers\Api\Gynecologue;

use App\Enums\StatutRendezVous;
use App\Http\Controllers\Controller;
use App\Http\Requests\Gynecologue\TraiterRendezVousRequest;
use App\Http\Resources\RendezVousResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RendezVousGynecologueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rendezVous = $request->user('gynecologue')
            ->rendezVous()
            ->with('femme.user')
            ->orderByDesc('date_souhaitee')
            ->paginate(15);

        return response()->json(RendezVousResource::collection($rendezVous)->response()->getData(true));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $rendezVous = $request->user('gynecologue')
            ->rendezVous()
            ->with('femme.user')
            ->findOrFail($id);

        return response()->json(new RendezVousResource($rendezVous));
    }

    public function accepter(TraiterRendezVousRequest $request, int $id): JsonResponse
    {
        $rendezVous = $request->user('gynecologue')->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::EnAttente) {
            return response()->json(['message' => 'Ce rendez-vous ne peut plus être modifié.'], 422);
        }

        $rendezVous->update([
            'statut'           => StatutRendezVous::Accepte,
            'note_gynecologue' => $request->note_gynecologue,
            'date_confirmee'   => $request->date_confirmee ?? $rendezVous->date_souhaitee,
            'heure_confirmee'  => $request->heure_confirmee ?? $rendezVous->heure_souhaitee,
        ]);

        return response()->json([
            'message'     => 'Rendez-vous accepté.',
            'rendez_vous' => new RendezVousResource($rendezVous),
        ]);
    }

    public function refuser(TraiterRendezVousRequest $request, int $id): JsonResponse
    {
        $rendezVous = $request->user('gynecologue')->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::EnAttente) {
            return response()->json(['message' => 'Ce rendez-vous ne peut plus être modifié.'], 422);
        }

        $rendezVous->update([
            'statut'           => StatutRendezVous::Refuse,
            'note_gynecologue' => $request->note_gynecologue,
        ]);

        return response()->json([
            'message'     => 'Rendez-vous refusé.',
            'rendez_vous' => new RendezVousResource($rendezVous),
        ]);
    }

    public function terminer(Request $request, int $id): JsonResponse
    {
        $rendezVous = $request->user('gynecologue')->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::Accepte) {
            return response()->json(['message' => 'Seuls les rendez-vous acceptés peuvent être terminés.'], 422);
        }

        $rendezVous->update(['statut' => StatutRendezVous::Termine]);

        return response()->json(['message' => 'Rendez-vous terminé.']);
    }
}
