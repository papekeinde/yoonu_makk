<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecommandationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommandationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $recommandations = $request->user()->femme
            ->recommandations()
            ->with('gynecologue')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(RecommandationResource::collection($recommandations)->response()->getData(true));
    }

    public function marquerLu(Request $request, int $id): JsonResponse
    {
        $recommandation = $request->user()->femme->recommandations()->findOrFail($id);

        $recommandation->update(['est_lu' => true]);

        return response()->json(['message' => 'Recommandation marquée comme lue.']);
    }
}
