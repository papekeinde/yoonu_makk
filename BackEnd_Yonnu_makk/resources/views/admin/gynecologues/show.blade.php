@extends('layouts.dashboard')

@section('title', 'Détails gynécologue')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.gynecologues.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-yoonu-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Retour à la liste
        </a>
        @if ($gynecologue->is_active)
            <form method="POST" action="{{ route('admin.gynecologues.desactiver', $gynecologue->id) }}">
                @csrf @method('PATCH')
                <button class="rounded-xl bg-rapide-500 hover:bg-rapide-700 text-white px-4 py-2 text-sm font-medium transition">Désactiver le compte</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.gynecologues.activer', $gynecologue->id) }}">
                @csrf @method('PATCH')
                <button class="rounded-xl bg-standard-500 hover:bg-standard-700 text-white px-4 py-2 text-sm font-medium transition">Activer le compte</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6 text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-yoonu-500 text-white text-2xl font-bold font-display">
                {{ strtoupper(mb_substr($gynecologue->prenom ?? 'G', 0, 1)) }}{{ strtoupper(mb_substr($gynecologue->nom ?? '', 0, 1)) }}
            </span>
            <h2 class="mt-4 text-lg font-bold text-gray-900 dark:text-white font-display">Dr {{ $gynecologue->prenom }} {{ $gynecologue->nom }}</h2>
            <p class="text-sm text-gray-500">{{ $gynecologue->email }}</p>
            <div class="mt-3 flex justify-center">
                <x-status-badge :color="$gynecologue->is_active ? 'green' : 'gray'" :label="$gynecologue->is_active ? 'Actif' : 'Inactif'" />
            </div>
            <p class="mt-4 text-sm text-gray-400">{{ $gynecologue->rendez_vous_count }} rendez-vous</p>
        </div>

        <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Informations professionnelles</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                @php
                    $infos = [
                        'Téléphone'         => $gynecologue->telephone ?? '—',
                        'Numéro d\'ordre'    => $gynecologue->numero_ordre ?? '—',
                        'Spécialité'        => $gynecologue->specialite ?? '—',
                        'Années d\'exp.'     => $gynecologue->annees_experience ?? '—',
                        'Structure'         => $gynecologue->structure_sante ?? '—',
                        'Ville'             => $gynecologue->ville ?? '—',
                        'Tarif consultation' => $gynecologue->tarif_consultation ? number_format($gynecologue->tarif_consultation, 0, ',', ' ') . ' FCFA' : '—',
                        'Créé le'           => $gynecologue->created_at?->format('d/m/Y'),
                    ];
                @endphp
                @foreach ($infos as $label => $val)
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</dt>
                        <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>
            @if ($gynecologue->bio)
                <div class="mt-6">
                    <dt class="text-xs uppercase tracking-wider text-gray-400">Bio</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $gynecologue->bio }}</dd>
                </div>
            @endif
        </div>
    </div>
@endsection
