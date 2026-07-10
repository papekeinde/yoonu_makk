<?php

namespace App\Http\Controllers\Web\Gynecologue;

use App\Enums\StatutRendezVous;
use App\Enums\TypeRecommandation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RendezVousController extends Controller
{
    public function index(): View
    {
        // La page affiche un calendrier FullCalendar ; les rendez-vous sont
        // chargés en AJAX via la route pro.rendez-vous.events.
        return view('pro.rendez-vous.index');
    }

    /**
     * Source AJAX du calendrier : rendez-vous du gynécologue connecté,
     * formatés pour FullCalendar (couleur + libellé par statut).
     */
    public function events(Request $request): JsonResponse
    {
        $couleurs = [
            'en_attente' => '#F59E0B', // ambre
            'accepte'    => '#22C55E', // vert
            'refuse'     => '#EF4444', // rouge
            'termine'    => '#E91E63', // rose de marque
            'annule'     => '#9CA3AF', // gris
        ];
        $libelles = [
            'en_attente' => 'En attente',
            'accepte'    => 'Accepté',
            'refuse'     => 'Refusé',
            'termine'    => 'Terminé',
            'annule'     => 'Annulé',
        ];

        $rendezVous = Auth::guard('gynecologue_web')->user()
            ->rendezVous()
            ->with('femme.user')
            ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
            ->get();

        $events = $rendezVous->map(function ($rdv) use ($couleurs, $libelles) {
            $statut     = $rdv->statut?->value ?? 'en_attente';
            $estAccepte = $statut === 'accepte';

            $date = ($estAccepte && $rdv->date_confirmee ? $rdv->date_confirmee : $rdv->date_souhaitee)?->format('Y-m-d');
            if (! $date) {
                return null;
            }

            $heure = $estAccepte && $rdv->heure_confirmee ? $rdv->heure_confirmee : $rdv->heure_souhaitee;
            $heure = $heure ? substr($heure, 0, 5) : null;

            $couleur  = $couleurs[$statut] ?? '#9CA3AF';
            $patiente = trim(($rdv->femme?->user?->prenom ?? '').' '.($rdv->femme?->user?->nom ?? '')) ?: 'Patiente';

            return [
                'id'              => $rdv->id,
                'title'           => $patiente,
                'start'           => $heure ? $date.'T'.$heure : $date,
                'allDay'          => $heure === null,
                'backgroundColor' => $couleur,
                'borderColor'     => $couleur,
                'extendedProps'   => [
                    'patiente' => $patiente,
                    'email'    => $rdv->femme?->user?->email,
                    'motif'    => $rdv->motif,
                    'statut'   => $libelles[$statut] ?? $statut,
                    'heure'    => $heure,
                    'showUrl'  => route('pro.rendez-vous.show', $rdv->id),
                ],
            ];
        })->filter()->values();

        return response()->json($events);
    }

    public function show(int $id): View
    {
        $gynecologue = Auth::guard('gynecologue_web')->user();

        $rendezVous = $gynecologue->rendezVous()
            ->with('femme.user')
            ->findOrFail($id);

        $femme = $rendezVous->femme;

        $symptomes = $femme
            ? $femme->symptomes()->with('entrees')->orderByDesc('date_journal')->take(10)->get()
            : collect();

        $grossesse = $femme?->grossesseActive;

        return view('pro.rendez-vous.show', compact('rendezVous', 'femme', 'symptomes', 'grossesse'));
    }

    public function accepter(Request $request, int $id): RedirectResponse
    {
        $rendezVous = Auth::guard('gynecologue_web')->user()->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::EnAttente) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être modifié.');
        }

        $data = $request->validate([
            'note_gynecologue' => ['nullable', 'string', 'max:1000'],
            'date_confirmee'   => ['nullable', 'date'],
            'heure_confirmee'  => ['nullable', 'string'],
        ]);

        $rendezVous->update([
            'statut'           => StatutRendezVous::Accepte,
            'note_gynecologue' => $data['note_gynecologue'] ?? null,
            'date_confirmee'   => $data['date_confirmee'] ?? $rendezVous->date_souhaitee,
            'heure_confirmee'  => $data['heure_confirmee'] ?? $rendezVous->heure_souhaitee,
        ]);

        return back()->with('success', 'Rendez-vous accepté.');
    }

    public function refuser(Request $request, int $id): RedirectResponse
    {
        $rendezVous = Auth::guard('gynecologue_web')->user()->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::EnAttente) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être modifié.');
        }

        $data = $request->validate([
            'note_gynecologue' => ['nullable', 'string', 'max:1000'],
        ]);

        $rendezVous->update([
            'statut'           => StatutRendezVous::Refuse,
            'note_gynecologue' => $data['note_gynecologue'] ?? null,
        ]);

        return back()->with('success', 'Rendez-vous refusé.');
    }

    public function terminer(int $id): RedirectResponse
    {
        $rendezVous = Auth::guard('gynecologue_web')->user()->rendezVous()->findOrFail($id);

        if ($rendezVous->statut !== StatutRendezVous::Accepte) {
            return back()->with('error', 'Seuls les rendez-vous acceptés peuvent être terminés.');
        }

        $rendezVous->update(['statut' => StatutRendezVous::Termine]);

        return back()->with('success', 'Rendez-vous terminé.');
    }

    public function recommander(Request $request, int $id): RedirectResponse
    {
        $gynecologue = Auth::guard('gynecologue_web')->user();

        $rendezVous = $gynecologue->rendezVous()->findOrFail($id);

        $data = $request->validate([
            'type'  => ['required', Rule::enum(TypeRecommandation::class)],
            'titre' => ['required', 'string', 'max:150'],
            'corps' => ['required', 'string', 'max:2000'],
        ]);

        $rendezVous->femme->recommandations()->create([
            ...$data,
            'genere_par'     => 'gynecologue',
            'gynecologue_id' => $gynecologue->id,
        ]);

        return back()->with('success', 'Recommandation envoyée à la patiente.');
    }
}
