<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RendezVousResource;
use App\Models\RendezVous;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RendezVousAdminController extends Controller
{
    /**
     * Liste tous les rendez-vous (pour FullCalendar / tableau de bord admin).
     * Filtres GET : statut, gynecologue_id, date_debut, date_fin, per_page
     */
    public function index(Request $request): JsonResponse
    {
        $rdvs = RendezVous::with(['gynecologue', 'femme'])
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->when($request->gynecologue_id, fn ($q, $id) => $q->where('gynecologue_id', $id))
            ->when($request->date_debut, fn ($q, $d) => $q->whereDate('date_souhaitee', '>=', $d))
            ->when($request->date_fin,   fn ($q, $d) => $q->whereDate('date_souhaitee', '<=', $d))
            ->orderByDesc('date_souhaitee')
            ->paginate($request->integer('per_page', 50));

        return response()->json(RendezVousResource::collection($rdvs)->response()->getData(true));
    }
}
