<?php

namespace App\Http\Controllers\Api\Gynecologue\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\GynecologueResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshTokenGynecologueController extends Controller
{
    public function refresh(Request $request): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        $gynecologue->currentAccessToken()->delete();
        $token = $gynecologue->createToken('gynecologue')->plainTextToken;

        return response()->json([
            'message'     => 'Token régénéré.',
            'gynecologue' => new GynecologueResource($gynecologue),
            'token'       => $token,
        ]);
    }
}
