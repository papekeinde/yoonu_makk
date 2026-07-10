<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $videos = Video::with('categorie')
            ->when($request->categorie_id, fn($q, $c) => $q->where('categorie_id', $c))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(VideoResource::collection($videos)->response()->getData(true));
    }

    public function store(StoreVideoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['auteur_id'] = $request->user()->id;
        $data['slug'] = Str::slug($request->titre) . '-' . Str::random(5);

        if ($request->hasFile('miniature')) {
            $data['miniature'] = $request->file('miniature')->store('miniatures', 'public');
        }

        if ($request->est_publie) {
            $data['publie_le'] = now();
        }

        $video = Video::create($data);
        $video->load('categorie');

        return response()->json([
            'message' => 'Vidéo créée.',
            'video'   => new VideoResource($video),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(new VideoResource(Video::with('categorie')->findOrFail($id)));
    }

    public function update(StoreVideoRequest $request, int $id): JsonResponse
    {
        $video = Video::findOrFail($id);
        $data = $request->validated();

        if (isset($data['titre'])) {
            $data['slug'] = Str::slug($data['titre']) . '-' . Str::random(5);
        }

        if ($request->hasFile('miniature')) {
            $data['miniature'] = $request->file('miniature')->store('miniatures', 'public');
        }

        if (isset($data['est_publie']) && $data['est_publie'] && ! $video->est_publie) {
            $data['publie_le'] = now();
        }

        $video->update($data);
        $video->load('categorie');

        return response()->json([
            'message' => 'Vidéo mise à jour.',
            'video'   => new VideoResource($video),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Video::findOrFail($id)->delete();

        return response()->json(['message' => 'Vidéo supprimée.']);
    }
}
