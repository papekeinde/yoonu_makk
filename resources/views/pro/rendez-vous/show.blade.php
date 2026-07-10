@extends('layouts.pro')

@section('title', 'Détails du rendez-vous')

@php
    $colors = [
        'en_attente' => 'amber',
        'accepte'    => 'green',
        'refuse'     => 'red',
        'termine'    => 'brand',
        'annule'     => 'gray',
    ];
    $labels = [
        'en_attente' => 'En attente', 'accepte' => 'Accepté', 'refuse' => 'Refusé',
        'termine' => 'Terminé', 'annule' => 'Annulé',
    ];
    $statut = $rendezVous->statut?->value;
    $patiente = $femme?->user;
    $typesReco = [
        'nutrition' => 'Nutrition', 'activite_physique' => 'Activité physique', 'hygiene_vie' => 'Hygiène de vie',
        'consultation' => 'Consultation', 'prenatal' => 'Prénatal', 'allaitement' => 'Allaitement',
        'preparation_accouchement' => 'Préparation accouchement',
    ];
@endphp

@section('content')
    <div class="mb-5">
        <a href="{{ route('pro.rendez-vous.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-yoonu-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Retour aux rendez-vous
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Patiente + RDV --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-yoonu-500 text-white font-bold font-display">
                            {{ strtoupper(mb_substr($patiente?->prenom ?? 'P', 0, 1)) }}{{ strtoupper(mb_substr($patiente?->nom ?? '', 0, 1)) }}
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white font-display">{{ $patiente?->prenom }} {{ $patiente?->nom }}</h2>
                            <p class="text-sm text-gray-500">{{ $patiente?->email }}</p>
                        </div>
                    </div>
                    <x-status-badge :color="$colors[$statut] ?? 'gray'" :label="$labels[$statut] ?? $statut" />
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @php
                        $infos = [
                            'Téléphone'       => $patiente?->telephone ?? '—',
                            'Date souhaitée'  => $rendezVous->date_souhaitee?->format('d/m/Y') . ($rendezVous->heure_souhaitee ? ' · ' . $rendezVous->heure_souhaitee : ''),
                            'Date confirmée'  => $rendezVous->date_confirmee?->format('d/m/Y') . ($rendezVous->heure_confirmee ? ' · ' . $rendezVous->heure_confirmee : '') ?: '—',
                            'Profil'          => $femme?->type_profil?->value ?? '—',
                        ];
                    @endphp
                    @foreach ($infos as $label => $val)
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $val }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($rendezVous->motif)
                    <div class="mt-4">
                        <dt class="text-xs uppercase tracking-wider text-gray-400">Motif</dt>
                        <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $rendezVous->motif }}</dd>
                    </div>
                @endif
                @if ($rendezVous->note_gynecologue)
                    <div class="mt-4 rounded-xl bg-gray-50 dark:bg-white/[0.03] p-3">
                        <dt class="text-xs uppercase tracking-wider text-gray-400">Votre note</dt>
                        <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $rendezVous->note_gynecologue }}</dd>
                    </div>
                @endif
            </div>

            {{-- Grossesse active --}}
            @if ($grossesse)
                <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4 font-display">Grossesse en cours</h3>
                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">Semaines</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white font-semibold">{{ $grossesse->semainesAmenorrhee() }} SA</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">Terme prévu</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $grossesse->date_accouchement_prevue?->format('d/m/Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">Groupe sanguin</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $grossesse->groupe_sanguin ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">J-accouchement</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ max(0, $grossesse->joursAvantAccouchement()) }} j</dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- Journal des symptômes --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Journal des symptômes <span class="text-gray-400 font-normal">(10 derniers)</span></h3>
                <div class="space-y-3">
                    @forelse ($symptomes as $s)
                        <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $s->date_journal?->format('d/m/Y') }}</p>
                                <span class="text-xs text-gray-400">{{ $s->entrees->count() }} entrée(s)</span>
                            </div>
                            @if ($s->note_generale)
                                <p class="mt-1 text-xs text-gray-500">{{ $s->note_generale }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-6">Aucun symptôme enregistré.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne actions --}}
        <div class="space-y-5">
            {{-- Traitement du rendez-vous --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Traitement</h3>
                @if ($statut === 'en_attente')
                    <form method="POST" action="{{ route('pro.rendez-vous.accepter', $rendezVous->id) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1">Date confirmée</label>
                            <input type="date" name="date_confirmee" value="{{ $rendezVous->date_souhaitee?->format('Y-m-d') }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1">Heure confirmée</label>
                            <input type="time" name="heure_confirmee" value="{{ $rendezVous->heure_souhaitee }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                        </div>
                        <textarea name="note_gynecologue" rows="2" placeholder="Note (optionnelle)…"
                                  class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500"></textarea>
                        <button class="w-full rounded-xl bg-standard-500 hover:bg-standard-700 text-white px-4 py-2.5 text-sm font-medium transition">Accepter</button>
                    </form>
                    <form method="POST" action="{{ route('pro.rendez-vous.refuser', $rendezVous->id) }}" class="space-y-3 mt-3"
                          onsubmit="return confirm('Refuser ce rendez-vous ?')">
                        @csrf @method('PATCH')
                        <textarea name="note_gynecologue" rows="2" placeholder="Motif du refus (optionnel)…"
                                  class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500"></textarea>
                        <button class="w-full rounded-xl bg-urgence-500 hover:bg-urgence-700 text-white px-4 py-2.5 text-sm font-medium transition">Refuser</button>
                    </form>
                @elseif ($statut === 'accepte')
                    <form method="POST" action="{{ route('pro.rendez-vous.terminer', $rendezVous->id) }}"
                          onsubmit="return confirm('Marquer ce rendez-vous comme terminé ?')">
                        @csrf @method('PATCH')
                        <button class="w-full rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-4 py-2.5 text-sm font-medium transition">Marquer comme terminé</button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">Ce rendez-vous est {{ strtolower($labels[$statut] ?? $statut) }}.</p>
                @endif
            </div>

            {{-- Recommandation --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Envoyer une recommandation</h3>
                <form method="POST" action="{{ route('pro.rendez-vous.recommander', $rendezVous->id) }}" class="space-y-3">
                    @csrf
                    <select name="type" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                        @foreach ($typesReco as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="titre" required placeholder="Titre"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                    <textarea name="corps" rows="4" required placeholder="Conseil pour la patiente…"
                              class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500"></textarea>
                    <button class="w-full rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-4 py-2.5 text-sm font-medium transition">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
