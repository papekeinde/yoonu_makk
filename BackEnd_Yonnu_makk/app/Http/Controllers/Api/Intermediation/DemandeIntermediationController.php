<?php

namespace App\Http\Controllers\Api\Intermediation;

use App\Http\Controllers\Controller;
use App\Models\DemandeIntermediation;
use App\Models\Femme;
use App\Models\ProfessionnelSante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeIntermediationController extends Controller
{
    private function femme(): Femme
    {
        return Auth::user()->femme;
    }

    private function professionnelConnecte(): ProfessionnelSante
    {
        $gynecologue = request()->user('gynecologue');

        abort_unless($gynecologue, 401, 'Non authentifié.');

        return ProfessionnelSante::where('gynecologue_id', $gynecologue->id)->firstOrFail();
    }

    /** GET /api/intermediation/demandes — mes demandes */
    public function index(): JsonResponse
    {
        $demandes = $this->femme()
            ->demandes()
            ->with('professionnel')
            ->latest()
            ->get();

        return response()->json($demandes);
    }

    /** POST /api/intermediation/demandes — créer une demande */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'professionnel_id' => 'required|exists:professionnels_sante,id',
            'type_demande'     => 'required|in:consultation_en_ligne,consultation_cabinet,suivi_grossesse,suivi_menopause,urgence,deuxieme_avis,bilan_nutritionnel,soutien_psychologique',
            'message'          => 'nullable|string|max:1000',
            'motif'            => 'nullable|string|max:255',
            'date_souhaitee'   => 'nullable|date|after:today',
            'heure_souhaitee'  => 'nullable|date_format:H:i',
            'urgente'          => 'boolean',
        ]);

        $demande = $this->femme()->demandes()->create($validated);
        $demande->load('professionnel');

        return response()->json([
            'message' => 'Demande envoyée avec succès.',
            'demande' => $demande,
        ], 201);
    }

    /** DELETE /api/intermediation/demandes/{id} — annuler */
    public function destroy(DemandeIntermediation $demande): JsonResponse
    {
        abort_if($demande->femme_id !== $this->femme()->id, 403);
        abort_if(!in_array($demande->statut, ['en_attente']), 422, 'Impossible d\'annuler une demande déjà traitée.');

        $demande->update(['statut' => 'annule']);
        return response()->json(['message' => 'Demande annulée.']);
    }

    // ── Routes professionnel (répondre aux demandes) ──────────────────────

    /** GET /api/intermediation/pro/demandes — demandes reçues par le pro */
    public function indexPro(): JsonResponse
    {
        $professionnel = $this->professionnelConnecte();

        $demandes = $professionnel->demandes()
            ->with(['femme.user:id,nom,prenom,telephone'])
            ->latest()->get();

        return response()->json($demandes);
    }

    /** PUT /api/intermediation/pro/demandes/{id} — accepter/refuser */
    public function updatePro(Request $request, DemandeIntermediation $demande): JsonResponse
    {
        $professionnel = $this->professionnelConnecte();
        abort_if($demande->professionnel_id !== $professionnel->id, 403);

        $validated = $request->validate([
            'statut'                 => 'required|in:accepte,refuse,termine',
            'reponse_professionnel'  => 'nullable|string|max:1000',
            'date_confirmee'         => 'nullable|date',
            'heure_confirmee'        => 'nullable|date_format:H:i',
            'compte_rendu'           => 'nullable|string',
        ]);

        $demande->update(array_merge($validated, ['traitee_le' => now()]));

        // Incrémenter consultations si terminée
        if ($validated['statut'] === 'termine') {
            $professionnel->increment('nb_consultations');
        }

        return response()->json(['message' => 'Demande mise à jour.', 'demande' => $demande->fresh()]);
    }
}
