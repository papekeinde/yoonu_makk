<?php

namespace App\Http\Controllers\Api\Gynecologue\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConnexionRequest;
use App\Http\Resources\GynecologueResource;
use App\Models\Gynecologue;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ConnexionGynecologueController extends Controller
{
    public function store(ConnexionRequest $request): JsonResponse
    {
        $identifiant = trim((string) ($request->input('identifiant') ?? $request->input('email') ?? ''));
        $gynecologue = $this->findGynecologueByIdentifier($identifiant);

        if (! $gynecologue || ! Hash::check($request->password, $gynecologue->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        if (! $gynecologue->is_active) {
            return response()->json(['message' => 'Votre compte a été désactivé.'], 403);
        }

        // Supprimer les anciens tokens → 1 seul token actif
        $gynecologue->tokens()->delete();

        $token = $gynecologue->createToken('gynecologue')->plainTextToken;

        return response()->json([
            'message'      => 'Connexion réussie.',
            'gynecologue'  => new GynecologueResource($gynecologue),
            'token'        => $token,
        ]);
    }

    public function destroy(): JsonResponse
    {
        request()->user('gynecologue')->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    private function findGynecologueByIdentifier(string $identifiant): ?Gynecologue
    {
        if ($identifiant === '') {
            return null;
        }

        if (filter_var($identifiant, FILTER_VALIDATE_EMAIL)) {
            return Gynecologue::where('email', $identifiant)->first();
        }

        return Gynecologue::whereIn('telephone', PhoneNumber::candidates($identifiant))->first();
    }
}
