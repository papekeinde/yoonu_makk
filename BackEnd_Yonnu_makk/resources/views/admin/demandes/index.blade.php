@extends('layouts.dashboard')

@section('title', 'Demandes d\'adhésion')

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
@endphp

@section('content')
    <form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
        <select name="statut" class="rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
            <option value="">Tous les statuts</option>
            <option value="en_attente" @selected(request('statut')==='en_attente')>En attente</option>
            <option value="approuvee"  @selected(request('statut')==='approuvee')>Approuvées</option>
            <option value="rejetee"    @selected(request('statut')==='rejetee')>Rejetées</option>
        </select>
        <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition">Filtrer</button>
    </form>

    <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-5 py-3 font-medium">Demandeur</th>
                        <th class="px-5 py-3 font-medium">Spécialité</th>
                        <th class="px-5 py-3 font-medium">Ville</th>
                        <th class="px-5 py-3 font-medium">Statut</th>
                        <th class="px-5 py-3 font-medium">Reçue le</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                    @forelse ($demandes as $d)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $d->prenom }} {{ $d->nom }}</p>
                                <p class="text-xs text-gray-400">{{ $d->email }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $d->specialite ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $d->ville ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <x-status-badge :color="$colors[$d->statut?->value] ?? 'gray'" :label="$labels[$d->statut?->value] ?? $d->statut?->value" />
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $d->created_at?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.demandes.show', $d->id) }}"
                                   class="rounded-lg px-3 py-1.5 text-xs font-medium text-yoonu-600 hover:bg-yoonu-50 dark:hover:bg-white/[0.04] transition">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Aucune demande trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $demandes->links() }}</div>
@endsection
