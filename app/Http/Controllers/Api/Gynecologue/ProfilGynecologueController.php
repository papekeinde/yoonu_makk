<?php

namespace App\Http\Controllers\Api\Gynecologue;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gynecologue\ChangeMotDePasseRequest;
use App\Http\Requests\Gynecologue\UpdateProfilGynecologueRequest;
use App\Http\Resources\GynecologueResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilGynecologueController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(new GynecologueResource($request->user('gynecologue')));
    }

    public function update(UpdateProfilGynecologueRequest $request): JsonResponse
    {
        $request->user('gynecologue')->update($request->validated());

        return response()->json([
            'message'     => 'Profil mis à jour.',
            'gynecologue' => new GynecologueResource($request->user('gynecologue')->fresh()),
        ]);
    }

    public function changerMotDePasse(ChangeMotDePasseRequest $request): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        if (! Hash::check($request->mot_de_passe_actuel, $gynecologue->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect.'], 422);
        }

        $gynecologue->update(['password' => $request->nouveau_mot_de_passe]);

        return response()->json(['message' => 'Mot de passe modifié avec succès.']);
    }
}
