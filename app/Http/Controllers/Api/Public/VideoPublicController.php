<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoPublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $videos = Video::publie()
            ->with('categorie')
            ->when($request->categorie_id, fn($q, $c) => $q->where('categorie_id', $c))
            ->when($request->langue, fn($q, $l) => $q->where('langue', $l))
            ->orderByDesc('publie_le')
            ->paginate(15);

        return response()->json(VideoResource::collection($videos)->response()->getData(true));
    }

    public function show(string $slug): JsonResponse
    {
        $video = Video::publie()->with('categorie')->where('slug', $slug)->firstOrFail();

        return response()->json(new VideoResource($video));
    }
}
