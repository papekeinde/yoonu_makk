<?php

namespace App\Http\Controllers\Api\Gynecologue;

use App\Enums\Genre;
use App\Enums\RoleUtilisateur;
use App\Enums\TypeProfilFemme;
use App\Http\Controllers\Controller;
use App\Http\Requests\Gynecologue\StoreRecommandationRequest;
use App\Http\Resources\FemmeResource;
use App\Http\Resources\GrossesseResource;
use App\Http\Resources\RecommandationResource;
use App\Http\Resources\SuiviGrossesseResource;
use App\Http\Resources\SymptomeResource;
use App\Models\Femme;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatienteController extends Controller
{
    // ── Helper : vérifie que le gynécologue a accès à cette patiente ──────────
    private function verifierAcces($gynecologue, int $femmeId): bool
    {
        // Accès via prise en charge directe OU via un rendez-vous
        return $gynecologue->patientes()->where('femme_id', $femmeId)->exists()
            || $gynecologue->rendezVous()->where('femme_id', $femmeId)->exists();
    }

    // ── Liste des patientes prises en charge ──────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        $patientes = $gynecologue->patientes()
            ->with('user')
            ->paginate(20);

        return response()->json(FemmeResource::collection($patientes)->response()->getData(true));
    }

    // ── Ajouter une patiente existante par email ou téléphone ─────────────────
    public function ajouterPatiente(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['nullable', 'email', 'required_without:telephone'],
            'telephone'=> ['nullable', 'string', 'required_without:email'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $gynecologue = $request->user('gynecologue');

        $user = User::where(function ($q) use ($request) {
                    if ($request->email)     $q->orWhere('email',     $request->email);
                    if ($request->telephone) $q->orWhere('telephone', $request->telephone);
                })->where('role', RoleUtilisateur::Patient)->first();

        if (! $user) {
            return response()->json(['message' => 'Aucune patiente trouvée avec ces informations.'], 404);
        }

        $femme = $user->femme;
        if (! $femme) {
            return response()->json(['message' => 'Ce compte n\'est pas un profil féminin.'], 422);
        }

        if ($gynecologue->patientes()->where('femme_id', $femme->id)->exists()) {
            return response()->json(['message' => 'Cette patiente est déjà dans votre liste.'], 409);
        }

        $gynecologue->patientes()->attach($femme->id, [
            'date_prise_en_charge' => now()->toDateString(),
            'notes'                => $request->notes,
        ]);

        return response()->json([
            'message'  => 'Patiente ajoutée à votre liste de prise en charge.',
            'patiente' => new FemmeResource($femme->load('user')),
        ], 201);
    }

    // ── Créer un compte patiente et l'ajouter directement ────────────────────
    public function creerPatiente(Request $request): JsonResponse
    {
        $request->validate([
            'nom'            => ['required', 'string', 'max:100'],
            'prenom'         => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'unique:users,email'],
            'telephone'      => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'ville'          => ['nullable', 'string', 'max:100'],
            'type_profil'    => ['nullable', 'in:grossesse,menopause,general'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        $gynecologue = $request->user('gynecologue');

        DB::beginTransaction();
        try {
            $motDePasse = Str::random(12);

            $user = User::create([
                'role'           => RoleUtilisateur::Patient,
                'genre'          => Genre::Femme,
                'nom'            => $request->nom,
                'prenom'         => $request->prenom,
                'email'          => $request->email,
                'password'       => Hash::make($motDePasse),
                'telephone'      => $request->telephone,
                'date_naissance' => $request->date_naissance,
                'ville'          => $request->ville,
            ]);

            $femme = Femme::create([
                'user_id'     => $user->id,
                'type_profil' => TypeProfilFemme::tryFrom($request->type_profil ?? 'general')
                                    ?? TypeProfilFemme::General,
            ]);

            $gynecologue->patientes()->attach($femme->id, [
                'date_prise_en_charge' => now()->toDateString(),
                'notes'                => $request->notes,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la création du compte.'], 500);
        }

        return response()->json([
            'message'      => 'Compte créé et patiente ajoutée à votre liste.',
            'patiente'     => new FemmeResource($femme->load('user')),
            'mot_de_passe' => $motDePasse,   // à transmettre à la patiente
        ], 201);
    }

    // ── Retirer une patiente de la liste de prise en charge ──────────────────
    public function retirerPatiente(Request $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        $detached = $gynecologue->patientes()->detach($femmeId);

        if (! $detached) {
            return response()->json(['message' => 'Cette patiente ne figure pas dans votre liste.'], 404);
        }

        return response()->json(['message' => 'Patiente retirée de votre liste de prise en charge.']);
    }

    // ── Symptômes d'une patiente ──────────────────────────────────────────────
    public function symptomes(Request $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        if (! $this->verifierAcces($gynecologue, $femmeId)) {
            return response()->json(['message' => 'Accès non autorisé à cette patiente.'], 403);
        }

        $femme = Femme::findOrFail($femmeId);

        $symptomes = $femme->symptomes()
            ->with('entrees')
            ->orderByDesc('date_journal')
            ->paginate(15);

        return response()->json(SymptomeResource::collection($symptomes)->response()->getData(true));
    }

    // ── Recommandation pour une patiente ─────────────────────────────────────
    public function recommander(StoreRecommandationRequest $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        if (! $this->verifierAcces($gynecologue, $femmeId)) {
            return response()->json(['message' => 'Accès non autorisé à cette patiente.'], 403);
        }

        $femme = Femme::findOrFail($femmeId);

        $recommandation = $femme->recommandations()->create([
            ...$request->validated(),
            'genere_par'     => 'gynecologue',
            'gynecologue_id' => $gynecologue->id,
        ]);

        return response()->json([
            'message'         => 'Recommandation envoyée.',
            'recommandation'  => new RecommandationResource($recommandation),
        ], 201);
    }

    // ── Grossesse active d'une patiente ───────────────────────────────────────
    public function grossesse(Request $request, int $femmeId): JsonResponse
    {
        $gynecologue = $request->user('gynecologue');

        if (! $this->verifierAcces($gynecologue, $femmeId)) {
            return response()->json(['message' => 'Accès non autorisé à cette patiente.'], 403);
        }

        $femme     = Femme::findOrFail($femmeId);
        $grossesse = $femme->grossesseActive;

        if (! $grossesse) {
            return response()->json(['message' => 'Cette patiente n\'a pas de grossesse active.'], 404);
        }

        return response()->json([
            'grossesse' => new GrossesseResource($grossesse),
            'suivis'    => SuiviGrossesseResource::collection($grossesse->suivis()->orderByDesc('date_saisie')->get()),
        ]);
    }
}

