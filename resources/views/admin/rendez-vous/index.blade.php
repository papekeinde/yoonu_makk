@extends('layouts.dashboard')

@section('title', 'Rendez-vous')

@php
    $colors = [
        'en_attente' => 'amber',
        'accepte'    => 'green',
        'refuse'     => 'red',
        'termine'    => 'brand',
        'annule'     => 'gray',
    ];
    $labels = [
        'en_attente' => 'En attente',
        'accepte'    => 'Accepté',
        'refuse'     => 'Refusé',
        'termine'    => 'Terminé',
        'annule'     => 'Annulé',
    ];
@endphp

@section('content')
    <form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
        <select name="statut" class="rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
            <option value="">Tous les statuts</option>
            @foreach ($labels as $val => $label)
                <option value="{{ $val }}" @selected(request('statut')===$val)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition">Filtrer</button>
    </form>

    <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-5 py-3 font-medium">Patiente</th>
                        <th class="px-5 py-3 font-medium">Gynécologue</th>
                        <th class="px-5 py-3 font-medium">Date souhaitée</th>
                        <th class="px-5 py-3 font-medium">Motif</th>
                        <th class="px-5 py-3 font-medium">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                    @forelse ($rendezVous as $rdv)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3">
                                @if ($rdv->femme?->user)
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $rdv->femme->user->prenom }} {{ $rdv->femme->user->nom }}</p>
                                    <p class="text-xs text-gray-400">{{ $rdv->femme->user->email }}</p>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                @if ($rdv->gynecologue)
                                    Dr {{ $rdv->gynecologue->prenom }} {{ $rdv->gynecologue->nom }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                {{ $rdv->date_souhaitee?->format('d/m/Y') }}
                                @if ($rdv->heure_souhaitee) <span class="text-gray-400">· {{ $rdv->heure_souhaitee }}</span> @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500 max-w-[220px] truncate">{{ $rdv->motif ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <x-status-badge :color="$colors[$rdv->statut?->value] ?? 'gray'" :label="$labels[$rdv->statut?->value] ?? $rdv->statut?->value" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Aucun rendez-vous trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $rendezVous->links() }}</div>
@endsection
