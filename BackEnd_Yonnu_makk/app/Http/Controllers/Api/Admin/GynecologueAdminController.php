<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GynecologueResource;
use App\Models\Gynecologue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GynecologueAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $gynecologues = Gynecologue::query()
            ->when($request->ville, fn($q, $v) => $q->where('ville', $v))
            ->when($request->search, fn($q, $s) => $q->where('nom', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(GynecologueResource::collection($gynecologues)->response()->getData(true));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(new GynecologueResource(Gynecologue::findOrFail($id)));
    }

    public function destroy(int $id): JsonResponse
    {
        Gynecologue::findOrFail($id)->delete();

        return response()->json(['message' => 'Gynécologue supprimé.']);
    }

    public function activer(int $id): JsonResponse
    {
        $gynecologue = Gynecologue::findOrFail($id);
        $gynecologue->update(['is_active' => true]);

        return response()->json(['message' => 'Gynécologue activé.']);
    }

    public function desactiver(int $id): JsonResponse
    {
        $gynecologue = Gynecologue::findOrFail($id);
        $gynecologue->update(['is_active' => false]);

        return response()->json(['message' => 'Gynécologue désactivé.']);
    }
}
