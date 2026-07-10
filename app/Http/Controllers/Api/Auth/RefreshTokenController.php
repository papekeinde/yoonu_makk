<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Supprimer le token actuel et en créer un nouveau
        $user->currentAccessToken()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Token régénéré.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }
}
