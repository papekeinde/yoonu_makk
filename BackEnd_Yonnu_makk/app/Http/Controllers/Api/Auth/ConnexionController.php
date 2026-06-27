<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConnexionRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ConnexionController extends Controller
{
    public function store(ConnexionRequest $request): JsonResponse
    {
        $identifiant = trim((string) ($request->input('identifiant') ?? $request->input('email') ?? ''));
        $user = $this->findUserByIdentifier($identifiant);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

        Auth::login($user);

        /** @var \App\Models\User $user */
        $user = Auth::user()->load('femme');

        // Supprimer les anciens tokens → 1 seul token actif par utilisateur
        $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }

    public function destroy(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    private function findUserByIdentifier(string $identifiant): ?User
    {
        if ($identifiant === '') {
            return null;
        }

        if (filter_var($identifiant, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $identifiant)->first();
        }

        return User::whereIn('telephone', PhoneNumber::candidates($identifiant))->first();
    }
}
