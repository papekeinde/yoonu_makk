@extends('layouts.dashboard')

@section('title', 'Tableau de bord')

@section('content')
    @php
        // [libellé, valeur, sous-texte, route, accent ? 'brand'/'amber'/null]
        $cards = [
            ['Utilisateurs', $stats['utilisateurs'], $stats['patients'].' patientes', 'admin.utilisateurs.index', null],
            ['Gynécologues', $stats['gynecologues'], $stats['gynecologues_actifs'].' actifs', 'admin.gynecologues.index', null],
            ['Demandes en attente', $stats['demandes_en_attente'], 'à traiter', 'admin.demandes.index', $stats['demandes_en_attente'] > 0 ? 'amber' : null],
            ['Rendez-vous', $stats['rendez_vous'], $stats['rendez_vous_en_attente'].' en attente', 'admin.rendez-vous.index', null],
        ];
        $accentDot = ['brand' => 'bg-yoonu-500', 'amber' => 'bg-rapide-500'];
    @endphp

    {{-- Chiffres clés --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-px overflow-hidden rounded-2xl border border-gray-200 bg-gray-200 dark:border-white/[0.06] dark:bg-white/[0.06]">
        @foreach ($cards as [$label, $value, $sub, $route, $accent])
            <a href="{{ route($route) }}"
               class="group relative bg-white dark:bg-[#25101A] p-5 transition hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                <div class="flex items-center gap-2">
                    @if ($accent)<span class="h-1.5 w-1.5 rounded-full {{ $accentDot[$accent] }}"></span>@endif
                    <p class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</p>
                </div>
                <p class="mt-3 text-3xl font-extrabold text-gray-900 dark:text-white font-display tabular-nums">{{ number_format($value, 0, ',', ' ') }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $sub }}</p>
                <svg class="absolute right-5 top-5 w-4 h-4 text-gray-300 opacity-0 -translate-x-1 transition-all group-hover:opacity-100 group-hover:translate-x-0 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @endforeach
    </div>

    {{-- Répartition --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5 mt-5">
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-5 font-display">Profils des femmes</h3>
            @php
                $totalF = max(1, $stats['femmes']);
                $repartition = [
                    ['Enceintes', $stats['femmes_enceintes'], 'bg-yoonu-500'],
                    ['Ménopause', $stats['femmes_menopause'], 'bg-yoonu-200'],
                ];
            @endphp
            <div class="space-y-4">
                @foreach ($repartition as [$lbl, $val, $clr])
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-gray-500 dark:text-gray-400">{{ $lbl }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white tabular-nums">{{ $val }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 dark:bg-white/[0.06] overflow-hidden">
                            <div class="h-full {{ $clr }} rounded-full" style="width: {{ round($val / $totalF * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
                <p class="text-xs text-gray-400 pt-2">{{ $stats['grossesses_actives'] }} grossesse(s) active(s) en suivi.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-5 font-display">Contenus</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white font-display tabular-nums">{{ $stats['contenus'] }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">Articles · {{ $stats['contenus_publies'] }} publiés</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white font-display tabular-nums">{{ $stats['videos'] }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">Vidéos · {{ $stats['videos_publiees'] }} publiées</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-5 font-display">Dernières demandes</h3>
            <ul class="space-y-3.5">
                @forelse ($dernieresDemandes as $d)
                    @php
                        $statut = $d->statut?->value ?? $d->statut;
                        $color = match($statut) {
                            'en_attente' => 'amber',
                            'approuvee', 'approuve' => 'green',
                            default => 'red',
                        };
                    @endphp
                    <li class="flex items-center justify-between gap-2 text-sm">
                        <span class="text-gray-700 dark:text-gray-300 truncate">{{ $d->prenom ?? '' }} {{ $d->nom ?? '' }}</span>
                        <x-status-badge :color="$color" :label="str_replace('_', ' ', $statut)" class="capitalize" />
                    </li>
                @empty
                    <li class="text-sm text-gray-400">Aucune demande pour l'instant.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Derniers utilisateurs --}}
    <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] mt-5 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[0.06]">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 font-display">Derniers inscrits</h3>
            <a href="{{ route('admin.utilisateurs.index') }}" class="text-xs font-medium text-yoonu-600 hover:text-yoonu-700">Tout voir</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-5 py-3 font-medium">Nom</th>
                        <th class="px-5 py-3 font-medium">Email</th>
                        <th class="px-5 py-3 font-medium">Rôle</th>
                        <th class="px-5 py-3 font-medium">Inscrit le</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                    @forelse ($derniersUtilisateurs as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $u->prenom }} {{ $u->nom }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $u->email }}</td>
                            <td class="px-5 py-3">
                                <x-status-badge :color="$u->role?->value === 'admin' ? 'brand' : 'gray'" :label="$u->role?->value ?? '—'" />
                            </td>
                            <td class="px-5 py-3 text-gray-500 tabular-nums">{{ $u->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400">Aucun utilisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
