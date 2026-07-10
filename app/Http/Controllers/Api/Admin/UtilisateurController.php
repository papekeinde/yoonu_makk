<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when($request->search, fn($q, $s) => $q->where('nom', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(UserResource::collection($users)->response()->getData(true));
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with('femme')->findOrFail($id);

        return response()->json(new UserResource($user));
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }
}
