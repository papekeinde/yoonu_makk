<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategorieRequest;
use App\Http\Resources\CategorieContenuResource;
use App\Models\CategorieContenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategorieContenuController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = CategorieContenu::withCount(['contenus', 'videos'])->get();

        return response()->json(CategorieContenuResource::collection($categories));
    }

    public function store(StoreCategorieRequest $request): JsonResponse
    {
        $categorie = CategorieContenu::create([
            ...$request->validated(),
            'slug' => Str::slug($request->nom),
        ]);

        return response()->json([
            'message'   => 'Catégorie créée.',
            'categorie' => new CategorieContenuResource($categorie),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(new CategorieContenuResource(CategorieContenu::findOrFail($id)));
    }

    public function update(StoreCategorieRequest $request, int $id): JsonResponse
    {
        $categorie = CategorieContenu::findOrFail($id);
        $categorie->update([
            ...$request->validated(),
            'slug' => Str::slug($request->nom),
        ]);

        return response()->json([
            'message'   => 'Catégorie mise à jour.',
            'categorie' => new CategorieContenuResource($categorie),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        CategorieContenu::findOrFail($id)->delete();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}
