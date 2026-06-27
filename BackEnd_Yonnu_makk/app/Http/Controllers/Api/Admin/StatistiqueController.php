<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contenu;
use App\Models\DemandeAdhesion;
use App\Models\Femme;
use App\Models\Grossesse;
use App\Models\Gynecologue;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

class StatistiqueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'utilisateurs'            => User::count(),
            'patients'                => User::where('role', 'patient')->count(),
            'femmes'                  => Femme::count(),
            'femmes_menopause'        => Femme::where('type_profil', 'menopause')->count(),
            'femmes_enceintes'        => Femme::where('type_profil', 'grossesse')->count(),
            'grossesses_actives'      => Grossesse::where('grossesse_active', true)->count(),
            'gynecologues'            => Gynecologue::count(),
            'gynecologues_actifs' => Gynecologue::where('is_active', true)->count(),
            'demandes_en_attente' => DemandeAdhesion::where('statut', 'en_attente')->count(),
            'rendez_vous'        => RendezVous::count(),
            'rendez_vous_en_attente' => RendezVous::where('statut', 'en_attente')->count(),
            'contenus'           => Contenu::count(),
            'contenus_publies'   => Contenu::where('est_publie', true)->count(),
            'videos'             => Video::count(),
            'videos_publiees'    => Video::where('est_publie', true)->count(),
        ]);
    }
}
