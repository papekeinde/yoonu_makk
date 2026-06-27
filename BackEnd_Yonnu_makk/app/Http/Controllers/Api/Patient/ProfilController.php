<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdateProfilRequest;
use App\Http\Resources\FemmeResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('femme');

        return response()->json([
            'user'  => new UserResource($user),
            'femme' => $user->femme ? new FemmeResource($user->femme) : null,
        ]);
    }

    public function update(UpdateProfilRequest $request): JsonResponse
    {
        $user = $request->user();
        $userData = $request->safe()->only(['nom', 'prenom', 'telephone', 'date_naissance', 'ville']);
        $femmeData = $request->safe()->only(['date_debut_menopause', 'stade_menopause', 'antecedents_medicaux']);

        if (! empty($userData)) {
            $user->update($userData);
        }

        if (! empty($femmeData) && $user->femme) {
            $user->femme->update($femmeData);
        }

        $user->load('femme');

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user'    => new UserResource($user),
            'femme'   => $user->femme ? new FemmeResource($user->femme) : null,
        ]);
    }

    public function avatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        $request->user()->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Avatar mis à jour.',
            'avatar'  => $path,
        ]);
    }
}
