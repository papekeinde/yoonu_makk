<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategorieRequest;
use App\Models\CategorieContenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategorieController extends Controller
{
    public function index(): View
    {
        $categories = CategorieContenu::withCount(['contenus', 'videos'])
            ->orderBy('nom')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategorieRequest $request): RedirectResponse
    {
        CategorieContenu::create([
            ...$request->validated(),
            'slug' => Str::slug($request->nom),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée.');
    }

    public function edit(int $id): View
    {
        $categorie = CategorieContenu::findOrFail($id);

        return view('admin.categories.edit', compact('categorie'));
    }

    public function update(StoreCategorieRequest $request, int $id): RedirectResponse
    {
        $categorie = CategorieContenu::findOrFail($id);
        $categorie->update([
            ...$request->validated(),
            'slug' => Str::slug($request->nom),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(int $id): RedirectResponse
    {
        CategorieContenu::findOrFail($id)->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée.');
    }
}
