<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategorieContenuResource;
use App\Http\Resources\ContenuResource;
use App\Models\CategorieContenu;
use App\Models\Contenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContenuPublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $contenus = Contenu::publie()
            ->with('categorie')
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->categorie_id, fn($q, $c) => $q->where('categorie_id', $c))
            ->when($request->langue, fn($q, $l) => $q->where('langue', $l))
            ->orderByDesc('publie_le')
            ->paginate(15);

        return response()->json(ContenuResource::collection($contenus)->response()->getData(true));
    }

    public function show(string $slug): JsonResponse
    {
        $contenu = Contenu::publie()->with('categorie')->where('slug', $slug)->firstOrFail();

        return response()->json(new ContenuResource($contenu));
    }

    public function categories(): JsonResponse
    {
        $categories = CategorieContenu::withCount([
            'contenus' => fn($q) => $q->where('est_publie', true),
        ])->get();

        return response()->json(CategorieContenuResource::collection($categories));
    }
}
