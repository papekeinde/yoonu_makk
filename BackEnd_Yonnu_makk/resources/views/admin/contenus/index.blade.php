@extends('layouts.dashboard')

@section('title', 'Contenus')

@php
    $typeLabels = ['article' => 'Article', 'conseil' => 'Conseil', 'faq' => 'FAQ'];
@endphp

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="type" class="rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                <option value="">Tous les types</option>
                @foreach ($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected(request('type')===$val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="categorie_id" class="rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                <option value="">Toutes les catégories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((int)request('categorie_id')===$cat->id)>{{ $cat->nom }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition">Filtrer</button>
        </form>
        <a href="{{ route('admin.contenus.create') }}" class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouveau contenu
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-5 py-3 font-medium">Titre</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium">Catégorie</th>
                        <th class="px-5 py-3 font-medium">Statut</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                    @forelse ($contenus as $c)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-white max-w-[280px] truncate">{{ $c->titre }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $typeLabels[$c->type?->value] ?? $c->type?->value }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $c->categorie?->nom ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <x-status-badge :color="$c->est_publie ? 'green' : 'gray'" :label="$c->est_publie ? 'Publié' : 'Brouillon'" />
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.contenus.edit', $c->id) }}"
                                       class="rounded-lg px-3 py-1.5 text-xs font-medium text-yoonu-600 hover:bg-yoonu-50 dark:hover:bg-white/[0.04] transition">Modifier</a>
                                    <form method="POST" action="{{ route('admin.contenus.destroy', $c->id) }}"
                                          onsubmit="return confirm('Supprimer ce contenu ?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Aucun contenu trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $contenus->links() }}</div>
@endsection
