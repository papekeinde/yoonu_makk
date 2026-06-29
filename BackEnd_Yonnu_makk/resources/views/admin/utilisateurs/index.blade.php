@extends('layouts.dashboard')

@section('title', 'Utilisateurs')

@section('content')
    {{-- Filtres --}}
    <form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[220px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, prénom, email…"
                   class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-yoonu-500 focus:border-transparent outline-none">
        </div>
        <select name="role" class="rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
            <option value="">Tous les rôles</option>
            <option value="patient" @selected(request('role')==='patient')>Patientes</option>
            <option value="admin"   @selected(request('role')==='admin')>Administrateurs</option>
        </select>
        <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition">Filtrer</button>
    </form>

    <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-5 py-3 font-medium">Nom</th>
                        <th class="px-5 py-3 font-medium">Email</th>
                        <th class="px-5 py-3 font-medium">Téléphone</th>
                        <th class="px-5 py-3 font-medium">Rôle</th>
                        <th class="px-5 py-3 font-medium">Inscrit le</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                    @forelse ($utilisateurs as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $u->prenom }} {{ $u->nom }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $u->email }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $u->telephone ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <x-status-badge :color="$u->role?->value === 'admin' ? 'brand' : 'gray'" :label="$u->role?->value ?? '—'" />
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $u->created_at?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.utilisateurs.show', $u->id) }}"
                                       class="rounded-lg px-3 py-1.5 text-xs font-medium text-yoonu-600 hover:bg-yoonu-50 dark:hover:bg-white/[0.04] transition">Voir</a>
                                    <form method="POST" action="{{ route('admin.utilisateurs.destroy', $u->id) }}"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Aucun utilisateur trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $utilisateurs->links() }}</div>
@endsection
