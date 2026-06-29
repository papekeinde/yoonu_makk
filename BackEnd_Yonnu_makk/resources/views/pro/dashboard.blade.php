@extends('layouts.pro')

@section('title', 'Tableau de bord')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white font-display">Bonjour, Dr {{ $gynecologue->prenom }}</h2>
        <p class="text-sm text-gray-500">Voici un aperçu de votre activité.</p>
    </div>

    {{-- Chiffres clés --}}
    @php
        // [libellé, valeur, accent ? 'amber'/null]
        $cards = [
            ['Demandes en attente', $stats['en_attente'], $stats['en_attente'] > 0 ? 'amber' : null],
            ['Rendez-vous acceptés', $stats['accepte'], null],
            ['Consultations terminées', $stats['termine'], null],
            ['Patientes suivies', $stats['patientes'], null],
        ];
        $accentDot = ['amber' => 'bg-rapide-500'];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-px overflow-hidden rounded-2xl border border-gray-200 bg-gray-200 dark:border-white/[0.06] dark:bg-white/[0.06] mb-6">
        @foreach ($cards as [$label, $value, $accent])
            <div class="bg-white dark:bg-[#25101A] p-5">
                <div class="flex items-center gap-2">
                    @if ($accent)<span class="h-1.5 w-1.5 rounded-full {{ $accentDot[$accent] }}"></span>@endif
                    <p class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white font-display tabular-nums">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Demandes à traiter --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 font-display">Demandes à traiter</h3>
                <a href="{{ route('pro.rendez-vous.index', ['statut' => 'en_attente']) }}" class="text-xs text-yoonu-600 hover:text-yoonu-700">Tout voir</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-white/[0.06] -my-3">
                @forelse ($aTraiter as $rdv)
                    <a href="{{ route('pro.rendez-vous.show', $rdv->id) }}" class="flex items-center justify-between gap-3 py-3 group">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-yoonu-700">{{ $rdv->femme?->user?->prenom }} {{ $rdv->femme?->user?->nom }}</p>
                            <p class="text-xs text-gray-400 tabular-nums">{{ $rdv->date_souhaitee?->format('d/m/Y') }} @if($rdv->heure_souhaitee) · {{ $rdv->heure_souhaitee }} @endif</p>
                        </div>
                        <x-status-badge color="amber" label="En attente" />
                    </a>
                @empty
                    <p class="text-center text-sm text-gray-400 py-6">Aucune demande en attente.</p>
                @endforelse
            </div>
        </div>

        {{-- Prochains rendez-vous --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 font-display">Prochains rendez-vous</h3>
                <a href="{{ route('pro.rendez-vous.index', ['statut' => 'accepte']) }}" class="text-xs text-yoonu-600 hover:text-yoonu-700">Tout voir</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-white/[0.06] -my-3">
                @forelse ($prochains as $rdv)
                    <a href="{{ route('pro.rendez-vous.show', $rdv->id) }}" class="flex items-center justify-between gap-3 py-3 group">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-yoonu-700">{{ $rdv->femme?->user?->prenom }} {{ $rdv->femme?->user?->nom }}</p>
                            <p class="text-xs text-gray-400 tabular-nums">{{ ($rdv->date_confirmee ?? $rdv->date_souhaitee)?->format('d/m/Y') }} @if($rdv->heure_confirmee) · {{ $rdv->heure_confirmee }} @endif</p>
                        </div>
                        <x-status-badge color="green" label="Accepté" />
                    </a>
                @empty
                    <p class="text-center text-sm text-gray-400 py-6">Aucun rendez-vous à venir.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
