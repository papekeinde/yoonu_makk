<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreSymptomeRequest;
use App\Http\Resources\SymptomeResource;
use App\Models\Symptome;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SymptomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $femme = $request->user()->femme;

        $symptomes = $femme->symptomes()
            ->with('entrees')
            ->orderByDesc('date_journal')
            ->paginate(15);

        return response()->json(SymptomeResource::collection($symptomes)->response()->getData(true));
    }

    public function store(StoreSymptomeRequest $request): JsonResponse
    {
        $femme = $request->user()->femme;

        $symptome = $femme->symptomes()->updateOrCreate(
            ['date_journal' => $request->date_journal],
            ['note_generale' => $request->note_generale],
        );

        $symptome->entrees()->delete();

        foreach ($request->entrees as $entree) {
            $symptome->entrees()->create($entree);
        }

        $symptome->load('entrees');

        return response()->json([
            'message'  => 'Symptômes enregistrés.',
            'symptome' => new SymptomeResource($symptome),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $symptome = $request->user()->femme
            ->symptomes()
            ->with('entrees')
            ->findOrFail($id);

        return response()->json(new SymptomeResource($symptome));
    }

    public function update(StoreSymptomeRequest $request, int $id): JsonResponse
    {
        $symptome = $request->user()->femme->symptomes()->findOrFail($id);

        $symptome->update([
            'date_journal'  => $request->date_journal,
            'note_generale' => $request->note_generale,
        ]);

        $symptome->entrees()->delete();

        foreach ($request->entrees as $entree) {
            $symptome->entrees()->create($entree);
        }

        $symptome->load('entrees');

        return response()->json([
            'message'  => 'Symptômes mis à jour.',
            'symptome' => new SymptomeResource($symptome),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $symptome = $request->user()->femme->symptomes()->findOrFail($id);
        $symptome->delete();

        return response()->json(['message' => 'Journal de symptômes supprimé.']);
    }
}
