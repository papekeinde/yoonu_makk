<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVideoRequest;
use App\Models\CategorieContenu;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $videos = Video::with('categorie')
            ->when($request->categorie_id, fn ($q, $c) => $q->where('categorie_id', $c))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = CategorieContenu::orderBy('nom')->get();

        return view('admin.videos.index', compact('videos', 'categories'));
    }

    public function create(): View
    {
        $categories = CategorieContenu::orderBy('nom')->get();

        return view('admin.videos.create', compact('categories'));
    }

    public function store(StoreVideoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['auteur_id'] = $request->user()->id;
        $data['slug'] = Str::slug($request->titre) . '-' . Str::random(5);

        if ($request->hasFile('miniature')) {
            $data['miniature'] = $request->file('miniature')->store('miniatures', 'public');
        }

        $data['est_publie'] = $request->boolean('est_publie');
        if ($data['est_publie']) {
            $data['publie_le'] = now();
        }

        Video::create($data);

        return redirect()->route('admin.videos.index')->with('success', 'Vidéo créée.');
    }

    public function edit(int $id): View
    {
        $video = Video::findOrFail($id);
        $categories = CategorieContenu::orderBy('nom')->get();

        return view('admin.videos.edit', compact('video', 'categories'));
    }

    public function update(StoreVideoRequest $request, int $id): RedirectResponse
    {
        $video = Video::findOrFail($id);
        $data = $request->validated();

        $data['slug'] = Str::slug($data['titre']) . '-' . Str::random(5);

        if ($request->hasFile('miniature')) {
            $data['miniature'] = $request->file('miniature')->store('miniatures', 'public');
        }

        $estPublie = $request->boolean('est_publie');
        if ($estPublie && ! $video->est_publie) {
            $data['publie_le'] = now();
        }
        $data['est_publie'] = $estPublie;

        $video->update($data);

        return redirect()->route('admin.videos.index')->with('success', 'Vidéo mise à jour.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Video::findOrFail($id)->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Vidéo supprimée.');
    }
}
