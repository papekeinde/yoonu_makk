<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contenu;
use App\Models\DemandeAdhesion;
use App\Models\Femme;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\View\View;

class StatistiqueController extends Controller
{
    public function index(): View
    {
        // Inscriptions des 12 derniers mois (calculé en PHP pour rester
        // indépendant du SGBD — pas de fonction de date spécifique).
        $debut = Carbon::now()->startOfMonth()->subMonths(11);

        $inscriptions = User::where('created_at', '>=', $debut)
            ->get(['id', 'created_at'])
            ->groupBy(fn ($u) => $u->created_at->format('Y-m'));

        $moisLabels = [];
        $moisValeurs = [];
        for ($i = 0; $i < 12; $i++) {
            $mois = (clone $debut)->addMonths($i);
            $cle = $mois->format('Y-m');
            $moisLabels[] = ucfirst($mois->locale('fr')->translatedFormat('M Y'));
            $moisValeurs[] = $inscriptions->get($cle)?->count() ?? 0;
        }

        $totalFemmes = Femme::count();
        $enceintes = Femme::where('type_profil', 'grossesse')->count();
        $menopause = Femme::where('type_profil', 'menopause')->count();

        $profils = [
            'labels' => ['Grossesse', 'Ménopause', 'Autre'],
            'data'   => [$enceintes, $menopause, max(0, $totalFemmes - $enceintes - $menopause)],
        ];

        $rdvParStatut = [
            'labels' => ['En attente', 'Accepté', 'Refusé', 'Terminé', 'Annulé'],
            'data'   => [
                RendezVous::where('statut', 'en_attente')->count(),
                RendezVous::where('statut', 'accepte')->count(),
                RendezVous::where('statut', 'refuse')->count(),
                RendezVous::where('statut', 'termine')->count(),
                RendezVous::where('statut', 'annule')->count(),
            ],
        ];

        $demandesParStatut = [
            'labels' => ['En attente', 'Approuvées', 'Rejetées'],
            'data'   => [
                DemandeAdhesion::where('statut', 'en_attente')->count(),
                DemandeAdhesion::where('statut', 'approuvee')->count(),
                DemandeAdhesion::where('statut', 'rejetee')->count(),
            ],
        ];

        $publications = [
            'labels'    => ['Articles', 'Vidéos'],
            'publies'   => [Contenu::where('est_publie', true)->count(), Video::where('est_publie', true)->count()],
            'brouillons' => [Contenu::where('est_publie', false)->count(), Video::where('est_publie', false)->count()],
        ];

        return view('admin.statistiques.index', compact(
            'moisLabels', 'moisValeurs', 'profils', 'rdvParStatut', 'demandesParStatut', 'publications'
        ));
    }
}
