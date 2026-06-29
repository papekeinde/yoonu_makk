@extends('layouts.dashboard')

@section('title', 'Détails utilisateur')

@section('content')
    <div class="mb-5">
        <a href="{{ route('admin.utilisateurs.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-yoonu-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6 text-center">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-yoonu-500 text-white text-2xl font-bold font-display">
                {{ strtoupper(mb_substr($utilisateur->prenom ?? 'U', 0, 1)) }}{{ strtoupper(mb_substr($utilisateur->nom ?? '', 0, 1)) }}
            </span>
            <h2 class="mt-4 text-lg font-bold text-gray-900 dark:text-white font-display">{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</h2>
            <p class="text-sm text-gray-500">{{ $utilisateur->email }}</p>
            <div class="mt-3 flex justify-center">
                <x-status-badge :color="$utilisateur->role?->value === 'admin' ? 'brand' : 'gray'" :label="$utilisateur->role?->value ?? '—'" />
            </div>
        </div>

        <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Informations</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                @php
                    $infos = [
                        'Téléphone'      => $utilisateur->telephone ?? '—',
                        'Genre'          => $utilisateur->genre?->value ?? '—',
                        'Date naissance' => $utilisateur->date_naissance?->format('d/m/Y') ?? '—',
                        'Ville'          => $utilisateur->ville ?? '—',
                        'Email vérifié'  => $utilisateur->email_verified_at?->format('d/m/Y') ?? 'Non vérifié',
                        'Inscrit le'     => $utilisateur->created_at?->format('d/m/Y H:i'),
                    ];
                @endphp
                @foreach ($infos as $label => $val)
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</dt>
                        <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($utilisateur->femme)
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mt-6 mb-4 font-display">Profil santé</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-gray-400">Type de profil</dt>
                        <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $utilisateur->femme->type_profil?->value ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-gray-400">Stade ménopause</dt>
                        <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $utilisateur->femme->stade_menopause?->value ?? '—' }}</dd>
                    </div>
                </dl>
            @endif
        </div>
    </div>
@endsection
