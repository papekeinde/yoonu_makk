<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\GynecologueResource;
use App\Models\Gynecologue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GynecologuePublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $gynecologues = Gynecologue::where('is_active', true)
            ->when($request->ville, fn($q, $v) => $q->where('ville', 'like', "%{$v}%"))
            ->when($request->specialite, fn($q, $s) => $q->where('specialite', 'like', "%{$s}%"))
            ->when($request->search, fn($q, $s) => $q->where('nom', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%"))
            ->orderBy('nom')
            ->paginate(15);

        return response()->json(GynecologueResource::collection($gynecologues)->response()->getData(true));
    }

    public function show(int $id): JsonResponse
    {
        $gynecologue = Gynecologue::where('is_active', true)->findOrFail($id);

        return response()->json(new GynecologueResource($gynecologue));
    }
}
