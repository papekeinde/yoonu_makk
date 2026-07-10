<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContenuRequest;
use App\Http\Requests\Admin\UpdateContenuRequest;
use App\Models\CategorieContenu;
use App\Models\Contenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContenuController extends Controller
{
    public function index(Request $request): View
    {
        $contenus = Contenu::with('categorie')
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->categorie_id, fn ($q, $c) => $q->where('categorie_id', $c))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = CategorieContenu::orderBy('nom')->get();

        return view('admin.contenus.index', compact('contenus', 'categories'));
    }

    public function create(): View
    {
        $categories = CategorieContenu::orderBy('nom')->get();

        return view('admin.contenus.create', compact('categories'));
    }

    public function store(StoreContenuRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['auteur_id'] = $request->user()->id;
        $data['slug'] = Str::slug($request->titre) . '-' . Str::random(5);

        if ($request->hasFile('image_couverture')) {
            $data['image_couverture'] = $request->file('image_couverture')->store('contenus', 'public');
        }

        $data['est_publie'] = $request->boolean('est_publie');
        if ($data['est_publie']) {
            $data['publie_le'] = now();
        }

        Contenu::create($data);

        return redirect()->route('admin.contenus.index')->with('success', 'Contenu créé.');
    }

    public function edit(int $id): View
    {
        $contenu = Contenu::findOrFail($id);
        $categories = CategorieContenu::orderBy('nom')->get();

        return view('admin.contenus.edit', compact('contenu', 'categories'));
    }

    public function update(UpdateContenuRequest $request, int $id): RedirectResponse
    {
        $contenu = Contenu::findOrFail($id);
        $data = $request->validated();

        if (isset($data['titre'])) {
            $data['slug'] = Str::slug($data['titre']) . '-' . Str::random(5);
        }

        if ($request->hasFile('image_couverture')) {
            $data['image_couverture'] = $request->file('image_couverture')->store('contenus', 'public');
        }

        $estPublie = $request->boolean('est_publie');
        if ($estPublie && ! $contenu->est_publie) {
            $data['publie_le'] = now();
        }
        $data['est_publie'] = $estPublie;

        $contenu->update($data);

        return redirect()->route('admin.contenus.index')->with('success', 'Contenu mis à jour.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Contenu::findOrFail($id)->delete();

        return redirect()->route('admin.contenus.index')->with('success', 'Contenu supprimé.');
    }
}
