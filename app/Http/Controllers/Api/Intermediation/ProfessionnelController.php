<?php

namespace App\Http\Controllers\Api\Intermediation;

use App\Http\Controllers\Controller;
use App\Models\ProfessionnelSante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionnelController extends Controller
{
    /** GET /api/intermediation/professionnels — liste filtrée */
    public function index(Request $request): JsonResponse
    {
        $query = ProfessionnelSante::actifs()->verifies()
            ->withCount('avis')
            ->with(['avis' => fn($q) => $q->where('approuve', true)->latest()->limit(3)]);

        if ($request->filled('profil')) {
            $query->pourProfil($request->profil);
        }
        if ($request->filled('type')) {
            $query->where('type_professionnel', $request->type);
        }
        if ($request->filled('ville')) {
            $query->parVille($request->ville);
        }
        if ($request->filled('en_ligne')) {
            $query->disponiblesEnLigne();
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('nom', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('specialite', 'like', "%{$s}%")
                ->orWhere('ville', 'like', "%{$s}%")
            );
        }

        $professionnels = $query->orderByDesc('note_moyenne')
                                ->orderByDesc('nb_consultations')
                                ->paginate(12);

        return response()->json($professionnels);
    }

    /** GET /api/intermediation/professionnels/{id} — fiche détail */
    public function show(ProfessionnelSante $professionnel): JsonResponse
    {
        $professionnel->load([
            'avis' => fn($q) => $q->where('approuve', true)
                                   ->with('femme.user:id,prenom')
                                   ->latest()->limit(10),
        ]);

        return response()->json($professionnel->append(['full_name','label_type']));
    }
}
