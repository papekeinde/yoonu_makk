@extends('layouts.dashboard')

@section('title', 'Détails de la demande')

@php
    $colors = [
        'en_attente' => 'amber',
        'approuvee'  => 'green',
        'rejetee'    => 'red',
    ];
    $labels = [
        'en_attente' => 'En attente',
        'approuvee'  => 'Approuvée',
        'rejetee'    => 'Rejetée',
    ];
    $statut = $demande->statut?->value;
@endphp

@section('content')
    <div class="mb-5">
        <a href="{{ route('admin.demandes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-yoonu-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Retour aux demandes
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white font-display">Dr {{ $demande->prenom }} {{ $demande->nom }}</h2>
                    <p class="text-sm text-gray-500">{{ $demande->email }}</p>
                </div>
                <x-status-badge :color="$colors[$statut] ?? 'gray'" :label="$labels[$statut] ?? $statut" />
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                @php
                    $infos = [
                        'Téléphone'        => $demande->telephone ?? '—',
                        'Numéro d\'ordre'   => $demande->numero_ordre ?? '—',
                        'Spécialité'       => $demande->specialite ?? '—',
                        'Années d\'exp.'    => $demande->annees_experience ?? '—',
                        'Structure'        => $demande->structure_sante ?? '—',
                        'Ville'            => $demande->ville ?? '—',
                    ];
                @endphp
                @foreach ($infos as $label => $val)
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</dt>
                        <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 flex flex-wrap gap-3">
                @if ($demande->chemin_diplome)
                    <a href="{{ asset('storage/' . $demande->chemin_diplome) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-sm text-yoonu-600 hover:underline">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Diplôme
                    </a>
                @endif
                @if ($demande->chemin_justificatif)
                    <a href="{{ asset('storage/' . $demande->chemin_justificatif) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-sm text-yoonu-600 hover:underline">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Justificatif
                    </a>
                @endif
            </div>

            @if ($demande->note_admin)
                <div class="mt-6 rounded-xl bg-gray-50 dark:bg-white/[0.03] p-4">
                    <dt class="text-xs uppercase tracking-wider text-gray-400">Note de l'administrateur</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $demande->note_admin }}</dd>
                </div>
            @endif

            @if ($demande->traite_le)
                <p class="mt-4 text-xs text-gray-400">
                    Traitée le {{ $demande->traite_le->format('d/m/Y H:i') }}
                    @if ($demande->traitePar) par {{ $demande->traitePar->prenom }} {{ $demande->traitePar->nom }} @endif
                </p>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6 h-fit">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Décision</h3>
            @if ($statut === 'en_attente')
                <form method="POST" action="{{ route('admin.demandes.approuver', $demande->id) }}" class="space-y-3"
                      onsubmit="return confirm('Approuver cette demande ? Un compte gynécologue sera créé.')">
                    @csrf @method('PATCH')
                    <textarea name="note_admin" rows="3" placeholder="Note (optionnelle)…"
                              class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500"></textarea>
                    <button class="w-full rounded-xl bg-standard-500 hover:bg-standard-700 text-white px-4 py-2.5 text-sm font-medium transition">Approuver</button>
                </form>
                <form method="POST" action="{{ route('admin.demandes.rejeter', $demande->id) }}" class="space-y-3 mt-3"
                      onsubmit="return confirm('Rejeter cette demande ?')">
                    @csrf @method('PATCH')
                    <textarea name="note_admin" rows="3" placeholder="Motif du rejet (optionnel)…"
                              class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yoonu-500"></textarea>
                    <button class="w-full rounded-xl bg-urgence-500 hover:bg-urgence-700 text-white px-4 py-2.5 text-sm font-medium transition">Rejeter</button>
                </form>
            @else
                <p class="text-sm text-gray-500">Cette demande a déjà été traitée.</p>
            @endif
        </div>
    </div>
@endsection
