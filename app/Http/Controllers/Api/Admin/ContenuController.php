<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContenuRequest;
use App\Http\Requests\Admin\UpdateContenuRequest;
use App\Http\Resources\ContenuResource;
use App\Models\Contenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $contenus = Contenu::with('categorie')
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->categorie_id, fn($q, $c) => $q->where('categorie_id', $c))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(ContenuResource::collection($contenus)->response()->getData(true));
    }

    public function store(StoreContenuRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['auteur_id'] = $request->user()->id;
        $data['slug'] = Str::slug($request->titre) . '-' . Str::random(5);

        if ($request->hasFile('image_couverture')) {
            $data['image_couverture'] = $request->file('image_couverture')->store('contenus', 'public');
        }

        if ($request->est_publie) {
            $data['publie_le'] = now();
        }

        $contenu = Contenu::create($data);
        $contenu->load('categorie');

        return response()->json([
            'message' => 'Contenu créé.',
            'contenu' => new ContenuResource($contenu),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(new ContenuResource(Contenu::with('categorie')->findOrFail($id)));
    }

    public function update(UpdateContenuRequest $request, int $id): JsonResponse
    {
        $contenu = Contenu::findOrFail($id);
        $data = $request->validated();

        if (isset($data['titre'])) {
            $data['slug'] = Str::slug($data['titre']) . '-' . Str::random(5);
        }

        if ($request->hasFile('image_couverture')) {
            $data['image_couverture'] = $request->file('image_couverture')->store('contenus', 'public');
        }

        if (isset($data['est_publie']) && $data['est_publie'] && ! $contenu->est_publie) {
            $data['publie_le'] = now();
        }

        $contenu->update($data);
        $contenu->load('categorie');

        return response()->json([
            'message' => 'Contenu mis à jour.',
            'contenu' => new ContenuResource($contenu),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Contenu::findOrFail($id)->delete();

        return response()->json(['message' => 'Contenu supprimé.']);
    }
}
